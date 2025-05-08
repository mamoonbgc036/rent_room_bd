<?php

namespace App\Livewire;

use App\Models\Payment;
use Carbon\Carbon;
use Livewire\Component;

class RevenueChart extends Component
{
    public $filterPeriod = 'month';
    public $chartData = [];

    public function mount($filterPeriod)
    {
        $this->filterPeriod = $filterPeriod;
        $this->loadChartData();
    }

    public function updatedFilterPeriod()
    {
        $this->loadChartData();
    }

    private function loadChartData()
    {
        $dateRange = $this->getDateRange();
        $groupBy = $this->getGroupBy();

        // Get rent revenue data
        $rentData = Payment::where('payment_type', 'rent')
            ->whereIn('status', ['completed', 'paid'])
            ->when($this->filterPeriod !== 'all', function($query) use ($dateRange) {
                return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->selectRaw("
                DATE_FORMAT(created_at, '{$groupBy}') as date,
                SUM(amount) as total
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Get booking revenue data
        $bookingData = Payment::where('payment_type', 'booking')
            ->whereIn('status', ['completed', 'paid'])
            ->when($this->filterPeriod !== 'all', function($query) use ($dateRange) {
                return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            })
            ->selectRaw("
                DATE_FORMAT(created_at, '{$groupBy}') as date,
                SUM(amount) as total
            ")
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Combine the data for the chart
        $dates = array_unique(array_merge(array_keys($rentData), array_keys($bookingData)));
        sort($dates);

        $this->chartData = collect($dates)->map(function($date) use ($rentData, $bookingData) {
            return [
                'date' => $date,
                'rent' => $rentData[$date] ?? 0,
                'booking' => $bookingData[$date] ?? 0,
                'total' => ($rentData[$date] ?? 0) + ($bookingData[$date] ?? 0)
            ];
        })->toArray();
    }

    private function getDateRange()
    {
        $now = now();

        return match($this->filterPeriod) {
            'month' => [
                'start' => $now->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
            ],
            'year' => [
                'start' => $now->startOfYear(),
                'end' => $now->copy()->endOfYear(),
            ],
            default => [
                'start' => null,
                'end' => null,
            ],
        };
    }

    private function getGroupBy()
    {
        return match($this->filterPeriod) {
            'month' => '%Y-%m-%d',    // Daily grouping for month view
            'year' => '%Y-%m',        // Monthly grouping for year view
            'all' => '%Y-%m',         // Monthly grouping for all time
        };
    }

    public function render()
    {
        return view('livewire.revenue-chart', [
            'chartData' => $this->chartData
        ]);
    }
}
