<?php

namespace LiveIntent\TelescopePrometheusExporter\Exporters;

use Laravel\Telescope\IncomingEntry;

class JobDurationHistogramExporter extends Exporter
{
    use RecordsJobMetrics;

    /**
     * Check if this exporter should export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return bool
     */
    public function shouldExport(IncomingEntry $entry)
    {
        return false;
    }

    /**
     * Export something for an entry.
     *
     * @param \Laravel\Telescope\IncomingEntry  $entry
     * @return void
     */
    public function export(IncomingEntry $entry)
    {
        $labels = [
            'service' => config('app.name'),
            'environment' => config('app.env'),
            'name' => $entry->content['name'],
            'attempts' => $entry->content['attempts'],
            'status' => $entry->content['status'],
        ];

        $histogram = $this->registry->getOrRegisterHistogram(
            'job',
            'execution_duration_milliseconds',
            'The job execution duration recorded in milliseconds.',
            array_keys($labels),
            $this->config['buckets']
        );

        $histogram->observe(
            $entry->content['duration'],
            array_values($labels)
        );
    }
}
