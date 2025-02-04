<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Files</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
     #status-message {
            margin-top: 10px;
            font-weight: bold;
            color: #007bff; /* Blue color */
        }
</style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Uploaded Files</h1>
        
        <!-- Button to go back to User List -->
        <a href="{{ route('user.index') }}" class="btn btn-primary mb-4" style="float: right;">User List</a>
        
        <!-- Form for uploading files -->
        <form action="#" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label for="file" class="form-label">Choose a file</label>
                <input type="file" name="file" class="form-control" id="file" required>
            </div>
            <button type="button" id="importButton" class="btn btn-primary">Import Users</button>
             <!-- Refresh Button with Icon -->
    <button id="refreshButton" class="btn btn-info me-3">
        <i class="fas fa-sync-alt"></i> <!-- Font Awesome Refresh Icon -->
    </button>
        </form>
  <!-- Status Message and Download Link -->
  <div class="d-flex align-items-center me-3">
        <div id="status-message" class="me-3"></div>
        <a id="download-link" href="#" class="btn btn-success" style="display: none;">Download File</a>
    </div>

   

        <!-- Display success message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="mt-4">List of Uploaded Files</h2>

        <!-- Table for displaying uploaded files -->
        @if(count($files) > 0)
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>File Name</th>
                        <th>File Path</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                        <tr>
                            <td>{{ $file->fileName }}</td>
                            <td>{{ $file->filePath }}</td>
                            <td>
                                <a href="{{ asset($file->filePath) }}" class="btn btn-sm btn-success" download>
                                    Download
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">No files uploaded yet.</p>
        @endif
    </div>

    <!-- Include Bootstrap JavaScript (optional, for interactivity) -->

    <script>
    $(document).ready(function () {
        $('#importButton').on('click', function () {
            var fileInput = $('#file')[0]; // Get the file input element
            if (fileInput.files.length === 0) {
                $('#status-message').text('Please select a file before importing.');
                return;
            }

            // Create FormData object
            var formData = new FormData();
            formData.append('file', fileInput.files[0]); // Add the selected file
            formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token

            // Perform AJAX request
            $.ajax({
                url: '{{ route("import-users") }}', // The route for file import
                method: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting content type
                success: function (response) {
                    if (response.status === 'processing') {
                        $('#status-message').text('Import is being processed. Please wait...');
                        const jobId = response.job_id; // Assume job_id is returned
                        pollJobStatus(jobId);
                    }
                },
                error: function (xhr) {
                    $('#status-message').text('Error during file import: ' + xhr.responseText);
                }
            });
        });

        function pollJobStatus(jobId) {
            // Start polling every 5 seconds
            const interval = setInterval(function () {
                $.ajax({
                    url: "{{ route('checkImportJobStatus', ':jobId') }}".replace(':jobId', jobId), // Replace :jobId with the actual jobId
                    method: 'GET',
                    success: function (response) {
                        if (response.status === 'completed') {
                            clearInterval(interval); // Stop polling
                            $('#status-message').text('Import complete!');

                            // Build the full file URL
                            // const fileUrl =  response.file_path;

                            // Update the download link
                            // $('#download-link')
                            //     .attr('href', fileUrl) // Set the file URL
                            //     .text('Download File') // Set link text
                            //     .show(); // Make the link visible
                        }
                    },
                    error: function () {
                        $('#status-message').text('Error checking import status.');
                    }
                });
            }, 5000); // Poll every 5 seconds
        }

        $('#refreshButton').on('click', function () {
            $('#file').val('');
            window.location.href = window.location.pathname; // This reloads the page without query parameters
        });
    });
</script>

</body>
</html>
