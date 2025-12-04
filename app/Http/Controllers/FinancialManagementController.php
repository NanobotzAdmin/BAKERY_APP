<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinancialManagementController extends Controller
{
    public function adminChartOfAccountIndex(){
        return view('financialManagement.adminChartOfAccountIndex');
    }

    public function adminTrialBalanceIndex(){
        return view('financialManagement.adminTrialBalanceIndex');
    }

    public function adminBalanceSheetIndex(){
        return view('financialManagement.adminBalanceSheetIndex');
    }

    public function adminProfitAndLostStatementIndex(){
        return view('financialManagement.adminProfitAndLostStatementIndex');
    }

    public function getBalanceSheetData(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // In a real application, you would query your database here using the dates.
        // For now, we will generate realistic dummy data.
        
        $data = $this->generateDummyBalanceSheetData($startDate, $endDate);

        return response()->json($data);
    }

    private function generateDummyBalanceSheetData($startDate, $endDate)
    {
        // Seed the random number generator to get consistent results for the same dates if needed, 
        // or just random for now.
        
        return [
            'assets' => [
                'current' => [
                    ['id' => 101, 'name' => 'Cash on Hand', 'balance' => rand(5000, 20000)],
                    ['id' => 102, 'name' => 'Bank - Checking', 'balance' => rand(50000, 150000)],
                    ['id' => 103, 'name' => 'Accounts Receivable', 'balance' => rand(30000, 80000)],
                    ['id' => 104, 'name' => 'Inventory', 'balance' => rand(100000, 250000)],
                    ['id' => 105, 'name' => 'Prepaid Expenses', 'balance' => rand(1000, 5000)],
                ],
                'non_current' => [
                    ['id' => 111, 'name' => 'Property, Plant & Equipment', 'balance' => rand(500000, 800000)],
                    ['id' => 112, 'name' => 'Accumulated Depreciation', 'balance' => -rand(50000, 150000)], // Negative
                    ['id' => 113, 'name' => 'Intangible Assets', 'balance' => rand(20000, 50000)],
                ],
                'total_current' => 0, // Calculated below
                'total_non_current' => 0, // Calculated below
                'total' => 0, // Calculated below
            ],
            'liabilities' => [
                'current' => [
                    ['id' => 201, 'name' => 'Accounts Payable', 'balance' => rand(20000, 60000)],
                    ['id' => 202, 'name' => 'Accrued Expenses', 'balance' => rand(5000, 15000)],
                    ['id' => 203, 'name' => 'Short-term Loans', 'balance' => rand(10000, 30000)],
                    ['id' => 204, 'name' => 'Tax Payable', 'balance' => rand(2000, 8000)],
                ],
                'non_current' => [
                    ['id' => 211, 'name' => 'Long-term Debt', 'balance' => rand(100000, 300000)],
                    ['id' => 212, 'name' => 'Deferred Tax Liability', 'balance' => rand(5000, 20000)],
                ],
                'total_current' => 0,
                'total_non_current' => 0,
                'total' => 0,
            ],
            'equity' => [
                'items' => [
                    ['id' => 301, 'name' => 'Share Capital', 'balance' => 500000], // Fixed
                    ['id' => 302, 'name' => 'Retained Earnings', 'balance' => rand(100000, 300000)],
                ],
                'total' => 0,
            ]
        ];

        // Calculate Totals (PHP side to ensure accuracy)
        // Note: In a real app, this would be done via DB aggregation or model logic.
        
        // This logic is simplified for the dummy data structure. 
        // We'll do a quick pass to sum things up.
        
        $data = $this->calculateTotals($data);
        
        return $data;
    }

    private function calculateTotals($data) {
        // Assets
        $data['assets']['total_current'] = array_sum(array_column($data['assets']['current'], 'balance'));
        $data['assets']['total_non_current'] = array_sum(array_column($data['assets']['non_current'], 'balance'));
        $data['assets']['total'] = $data['assets']['total_current'] + $data['assets']['total_non_current'];

        // Liabilities
        $data['liabilities']['total_current'] = array_sum(array_column($data['liabilities']['current'], 'balance'));
        $data['liabilities']['total_non_current'] = array_sum(array_column($data['liabilities']['non_current'], 'balance'));
        $data['liabilities']['total'] = $data['liabilities']['total_current'] + $data['liabilities']['total_non_current'];

        // Equity
        $data['equity']['total'] = array_sum(array_column($data['equity']['items'], 'balance'));

        // Force Balance (Assets = Liabilities + Equity) for the demo
        // We'll adjust Retained Earnings to make it balance.
        $diff = $data['assets']['total'] - ($data['liabilities']['total'] + $data['equity']['total']);
        
        foreach ($data['equity']['items'] as &$item) {
            if ($item['name'] === 'Retained Earnings') {
                $item['balance'] += $diff;
            }
        }
        // Recalculate Equity Total
        $data['equity']['total'] = array_sum(array_column($data['equity']['items'], 'balance'));

        return $data;
    }
}