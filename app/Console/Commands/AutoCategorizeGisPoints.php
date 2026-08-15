<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\PoiCategory;
use App\Models\GisPointOfInterest;
use App\Services\Gis\GeminiGisCategorizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

final class AutoCategorizeGisPoints extends Command
{
    protected $signature = 'gis:ai-categorize
        {--chunk=30 : Number of GIS points sent to Gemini per request}
        {--dry-run : Analyze all points without updating the database}';

    protected $description = 'Categorize every GIS point of interest using Google Gemini AI';

    public function handle(GeminiGisCategorizationService $categorizationService): int
    {
        $chunkSize = filter_var(
            $this->option('chunk'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 100]],
        );

        if ($chunkSize === false) {
            $this->components->error('The --chunk option must be an integer between 1 and 100.');

            return self::INVALID;
        }

        try {
            $total = GisPointOfInterest::query()->count();
        } catch (Throwable $exception) {
            $this->components->error('Unable to read GIS points: '.$exception->getMessage());

            return self::FAILURE;
        }

        if ($total === 0) {
            $this->components->info('No GIS points of interest were found.');

            return self::SUCCESS;
        }

        $this->components->info("Analyzing {$total} GIS points with Gemini in chunks of {$chunkSize}...");
        $analysisProgress = $this->output->createProgressBar($total);
        $analysisProgress->start();
        $assignments = [];

        try {
            GisPointOfInterest::query()
                ->select(['id', 'name'])
                ->chunkById($chunkSize, function (Collection $points) use (
                    $categorizationService,
                    &$assignments,
                    $analysisProgress,
                ): void {
                    $chunkAssignments = $categorizationService->categorize($points);

                    foreach ($chunkAssignments as $id => $category) {
                        if (isset($assignments[$id])) {
                            throw new \RuntimeException("Duplicate GIS assignment detected for ID [{$id}].");
                        }

                        $assignments[$id] = $category;
                    }

                    $analysisProgress->advance($points->count());
                });

            if (count($assignments) !== $total) {
                throw new \RuntimeException('Gemini did not categorize every GIS point.');
            }
        } catch (Throwable $exception) {
            $analysisProgress->finish();
            $this->newLine(2);
            $this->components->error('Categorization aborted without database changes: '.$exception->getMessage());

            return self::FAILURE;
        }

        $analysisProgress->finish();
        $this->newLine(2);

        if ((bool) $this->option('dry-run')) {
            $this->renderSummary($assignments);
            $this->components->info('Dry run completed. No database rows were changed.');

            return self::SUCCESS;
        }

        $this->components->info('Applying validated categories...');
        $updateProgress = $this->output->createProgressBar($total);
        $updateProgress->start();

        try {
            DB::transaction(function () use ($assignments, $updateProgress): void {
                $assignmentIds = array_keys($assignments);
                $existingCount = GisPointOfInterest::query()
                    ->whereKey($assignmentIds)
                    ->lockForUpdate()
                    ->count();

                if ($existingCount !== count($assignmentIds)) {
                    throw new \RuntimeException('One or more GIS points no longer exist.');
                }

                foreach ($assignments as $id => $category) {
                    GisPointOfInterest::query()
                        ->whereKey($id)
                        ->update([
                            'category' => $category->value,
                            'icon_marker' => $category->defaultMarker(),
                        ]);

                    $updateProgress->advance();
                }
            });
        } catch (Throwable $exception) {
            $updateProgress->finish();
            $this->newLine(2);
            $this->components->error('Database update rolled back: '.$exception->getMessage());

            return self::FAILURE;
        }

        $updateProgress->finish();
        $this->newLine(2);
        $this->renderSummary($assignments);
        $this->components->info("Successfully categorized {$total} GIS points.");

        return self::SUCCESS;
    }

    /**
     * @param  array<int, PoiCategory>  $assignments
     */
    private function renderSummary(array $assignments): void
    {
        $counts = array_fill_keys(
            array_map(static fn (PoiCategory $category): string => $category->value, PoiCategory::cases()),
            0,
        );

        foreach ($assignments as $category) {
            $counts[$category->value]++;
        }

        $this->table(
            ['Category', 'Total'],
            array_map(
                static fn (PoiCategory $category): array => [$category->label(), $counts[$category->value]],
                PoiCategory::cases(),
            ),
        );
    }
}
