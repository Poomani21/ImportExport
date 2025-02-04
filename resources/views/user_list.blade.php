<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Custom CSS */
        .table th, .table td {
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .pagination {
            justify-content: center;
        }
        .table-container {
            margin-top: 40px;
        }
        .table th, .table td {
            padding: 1.2rem;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        #status-message {
            margin-top: 10px;
            font-weight: bold;
            color: #007bff; /* Blue color */
        }
        #download-link {
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
<div class="container table-container">
    <h1 class="mb-4">Users List</h1>

    <!-- Buttons Row -->
    <div class="d-flex align-items-center mb-3">
    <!-- Search Form -->
    <form id="searchForm" class="d-flex me-3" method="GET" action="{{ route('user.index') }}">
        <input 
            type="text" 
            name="search" 
            id="searchInput" 
            class="form-control" 
            placeholder="Search by city, state, country, pincode, gender"
            value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary ms-2">Search</button>
    </form>

    <!-- Export Data Button -->
    <button id="exportButton" class="btn btn-secondary me-3">Export</button>

    <!-- Status Message and Download Link -->
    <div class="d-flex align-items-center me-3">
        <div id="status-message" class="me-3"></div>
        <a id="download-link" href="#" class="btn btn-success" style="display: none;">Download File</a>
    </div>

    <!-- Refresh Button with Icon -->
    <button id="refreshButton" class="btn btn-info me-3">
        <i class="fas fa-sync-alt"></i> <!-- Font Awesome Refresh Icon -->
    </button>

    <!-- Back Button -->
    <a href="{{ route('exportlist.list') }}" class="btn btn-secondary ms-auto">Back</a>
</div>


   

    <!-- Table for displaying users -->
    <table class="table table-bordered table-striped table-hover" id="usersTable">
        <thead class="table-dark">
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Description</th>
                <th>Pincode</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->gender }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->description }}</td>
                    <td>{{ $user->pincode }}</td>
                    <td>{{ $user->city }}</td>
                    <td>{{ $user->state }}</td>
                    <td>{{ $user->country }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Controls -->
    <div class="d-flex justify-content-center">
        {{ $users->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#exportButton').on('click', function () {
            // Trigger the export process
            var searchValue = $('#searchInput').val();

            $.ajax({
                url: '{{ route("ExportFilterData") }}', // The route that triggers the export
                method: 'GET',
                data: { 
                    search: searchValue, // Pass search data if needed
                    _token: '{{ csrf_token() }}' // Include CSRF token
                },
                success: function (response) {
                    if (response.status === 'processing') {
                        $('#status-message').text('Export is being processed. Please wait...');
                        const jobId = response.job_id; // Assume job_id is returned
                        pollJobStatus(jobId);
                    }
                },
                error: function () {
                    $('#status-message').text('Error starting export process.');
                }
            });
        });

        function pollJobStatus(jobId) {
            // Start polling every 5 seconds
            const interval = setInterval(function () {
                $.ajax({
                    url: "{{ route('checkJobStatus', ':jobId') }}".replace(':jobId', jobId), // Replace :jobId with the actual jobId
                    method: 'GET',
                    success: function (response) {
                        if (response.status === 'completed') {
                            clearInterval(interval); // Stop polling
                            $('#status-message').text('Export complete!');

                            // Build the full file URL
                            const fileUrl = "{{ asset('storage') }}/" + response.file_path;

                            // Update the download link
                                $('#download-link')
                                .attr('href', fileUrl) // Set the file URL
                                .text('Download File') // Set link text
                                .show(); // Make the link visible
                        }
                    },
                    error: function () {
                        $('#status-message').text('Error checking export status.');
                    }
                });
            }, 5000); // Poll every 5 seconds
        }

        $('#refreshButton').on('click', function () {
            $('#searchInput').val('');
            window.location.href = window.location.pathname; // This reloads the page without query parameters
        });

        
    });
   
    

</script>

</body>
</html>
