<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Log Report - {{ $date->format('Y-m-d') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        .summary {
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary h2 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 18px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .summary-item h3 {
            margin: 0 0 5px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        .summary-item .number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .section {
            margin-bottom: 30px;
        }
        .section h2 {
            margin: 0 0 15px 0;
            color: #2c3e50;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
                 .page-break {
             page-break-before: always;
         }
         .changes-row {
             background-color: #f0f8ff !important;
         }
         .changes-cell {
             padding: 5px 8px !important;
             font-size: 11px !important;
             color: #2c3e50 !important;
             border-left: 3px solid #3498db !important;
         }
         .footer {
             margin-top: 30px;
             text-align: center;
             font-size: 10px;
             color: #7f8c8d;
             border-top: 1px solid #ddd;
             padding-top: 10px;
         }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Log Report</h1>
        <p>School Management System</p>
        <p>Report Date: {{ $date->format('F j, Y') }}</p>
        <p>Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary">
        <h2>Summary</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <h3>Total Activity Logs</h3>
                <div class="number">{{ $activityLogs->count() }}</div>
            </div>
            <div class="summary-item">
                <h3>Total Error Logs</h3>
                <div class="number">{{ $errorLogs->count() }}</div>
            </div>
            <div class="summary-item">
                <h3>Total Logs</h3>
                <div class="number">{{ $report->total_logs }}</div>
            </div>
        </div>
    </div>

    @if($activityLogs->count() > 0)
    <div class="section">
        <h2>Activity Logs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>User Type</th>
                    <th>Action</th>
                    <th>Table Name</th>
                    <th>Record ID</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activityLogs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->user_id ?? 'N/A' }}</td>
                    <td>{{ ucfirst($log->user_type ?? 'N/A') }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->table_name ?? 'N/A' }}</td>
                    <td>{{ $log->record_id ?? 'N/A' }}</td>
                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                    <td>{{ Str::limit($log->user_agent ?? 'N/A', 25) }}</td>
                    <td>{{ $log->created_at->format('H:i:s') }}</td>
                </tr>
                @if($log->changes)
                <tr class="changes-row">
                    <td colspan="9" class="changes-cell">
                        <strong>Changes:</strong> {{ json_encode($log->changes) }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($errorLogs->count() > 0)
    <div class="section page-break">
        <h2>Error Logs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Code</th>
                    <th>File</th>
                    <th>Line</th>
                    <th>Message</th>
                    <th>URL</th>
                    <th>Method</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($errorLogs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->user_id ?? 'N/A' }}</td>
                    <td>{{ $log->code ?? 'N/A' }}</td>
                    <td>{{ $log->file ? basename($log->file) : 'N/A' }}</td>
                    <td>{{ $log->line ?? 'N/A' }}</td>
                    <td>{{ Str::limit($log->message, 50) }}</td>
                    <td>{{ Str::limit($log->url, 30) }}</td>
                    <td>{{ $log->method ?? 'N/A' }}</td>
                    <td>{{ $log->ip_address ?? 'N/A' }}</td>
                    <td>{{ Str::limit($log->user_agent ?? 'N/A', 25) }}</td>
                    <td>{{ $log->created_at->format('H:i:s') }}</td>
                </tr>
                @if($log->input)
                <tr class="changes-row">
                    <td colspan="11" class="changes-cell">
                        <strong>Input:</strong> {{ json_encode($log->input) }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by the School Management System</p>
        <p>For any questions, please contact the system administrator</p>
    </div>
</body>
</html>
