<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function weekly(Request $request)
    {
        [$start, $end] = $this->resolveWeekRange($request);

        $interventions = Intervention::with(['site', 'user'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $bySite = $interventions
            ->groupBy('site_id')
            ->map(fn ($group) => [
                'site' => $group->first()->site,
                'interventions' => $group->values(),
            ])
            ->values();

        $byUser = $interventions
            ->groupBy('user_id')
            ->map(fn ($group) => [
                'user' => $group->first()->user,
                'interventions' => $group->values(),
            ])
            ->values();

        return response()->json([
            'week_start' => $start->toDateString(),
            'week_end' => $end->toDateString(),
            'by_site' => $bySite,
            'by_user' => $byUser,
            'chronological' => $interventions->values(),
        ]);
    }

    public function exportCsv(Request $request)
    {
        [$start, $end] = $this->resolveWeekRange($request);
        $interventions = Intervention::with(['site', 'user'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $filename = sprintf('getsirarh-week-%s.csv', $start->toDateString());

        $callback = function () use ($interventions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'date',
                'site_number',
                'site_name',
                'user',
                'status',
                'problem_resolved',
                'unresolved_reason',
                'comment',
            ]);

            foreach ($interventions as $intervention) {
                fputcsv($handle, [
                    $intervention->created_at?->toDateTimeString(),
                    $intervention->site?->site_number,
                    $intervention->site?->name,
                    $intervention->user?->name,
                    $intervention->status,
                    $intervention->problem_resolved ? 'oui' : 'non',
                    $intervention->unresolved_reason,
                    $intervention->comment,
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        [$start, $end] = $this->resolveWeekRange($request);
        $interventions = Intervention::with(['site', 'user'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $lines = [
            'GETSIRARH - Rapport hebdomadaire',
            sprintf('Semaine du %s au %s', $start->toDateString(), $end->toDateString()),
            ' ',
        ];

        foreach ($interventions as $intervention) {
            $lines[] = sprintf(
                '%s | %s | %s | %s | %s',
                $intervention->created_at?->format('Y-m-d H:i'),
                $intervention->site?->site_number ?? '-',
                $intervention->site?->name ?? '-',
                $intervention->user?->name ?? '-',
                $intervention->status
            );
            if ($intervention->problem_resolved) {
                $lines[] = 'Résolu: oui';
            } else {
                $lines[] = 'Résolu: non';
                if ($intervention->unresolved_reason) {
                    $lines[] = 'Raison: '.$intervention->unresolved_reason;
                }
            }
            if ($intervention->comment) {
                $lines[] = 'Commentaire: '.$intervention->comment;
            }
            $lines[] = ' ';
        }

        $pdf = $this->buildPdf($lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="getsirarh-week-'.$start->toDateString().'.pdf"',
        ]);
    }

    private function resolveWeekRange(Request $request): array
    {
        $start = $request->filled('week_start')
            ? Carbon::parse($request->string('week_start'))
            : Carbon::now();

        $start = $start->startOfWeek();
        $end = (clone $start)->endOfWeek();

        return [$start, $end];
    }

    private function buildPdf(array $lines): string
    {
        $escaped = array_map(function ($line) {
            $line = str_replace('\\', '\\\\', $line);
            $line = str_replace('(', '\\(', $line);
            $line = str_replace(')', '\\)', $line);
            return $line;
        }, $lines);

        $content = "BT\n/F1 12 Tf\n50 790 Td\n";
        foreach ($escaped as $line) {
            $content .= '(' . $line . ") Tj\nT*\n";
        }
        $content .= "ET\n";

        $objects = [];
        $objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>";
        $objects[] = "<< /Length ".strlen($content)." >>\nstream\n".$content."endstream";
        $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n".$object."\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n".$xrefPosition."\n%%EOF\n";

        return $pdf;
    }
}
