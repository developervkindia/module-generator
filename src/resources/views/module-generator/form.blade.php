<!-- resources/views/module-generator/form.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e3f2fd; /* Light blue background */
            color: #0d6efd; /* Bootstrap primary color */
        }
        .form-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 50px;
        }
        .form-header {
            background-color: #0d6efd; /* Bootstrap primary color */
            color: #ffffff;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #0d6efd; /* Bootstrap primary color */
            border-color: #0d6efd; /* Bootstrap primary color */
        }
        .btn-primary:hover {
            background-color: #0b5ed7; /* Darker shade */
            border-color: #0a58ca; /* Darker shade */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-container">
                <div class="form-header">
                    <h2>Module Generator</h2>
                </div>
                <form action="{{ route('module-generator.generate') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="module_name" class="form-label">Module Name:</label>
                        <input type="text" name="module_name" id="module_name" class="form-control" required>
                    </div>

                    <h3>Fields</h3>
                    <div id="fields">
                        <div class="field mb-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="field_name[]" class="form-label">Field Name:</label>
                                    <input type="text" name="field_name[]" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="field_type[]" class="form-label">Field Type:</label>
                                    <select name="field_type[]" class="form-select" required>
                                        <option value="string">String</option>
                                        <option value="text">Text</option>
                                        <option value="integer">Integer</option>
                                        <option value="decimal">Decimal</option>
                                        <option value="boolean">Boolean</option>
                                        <option value="date">Date</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="nullable[]" class="form-label">Nullable:</label>
                                    <select name="nullable[]" class="form-select">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger" onclick="removeField(this)">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary mb-3" onclick="addField()">Add Field</button>
                    <br>
                    <button type="submit" class="btn btn-primary w-100">Generate Module</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function addField() {
        const fieldTemplate = `
            <div class="field mb-3">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="field_name[]" class="form-label">Field Name:</label>
                        <input type="text" name="field_name[]" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="field_type[]" class="form-label">Field Type:</label>
                        <select name="field_type[]" class="form-select" required>
                            <option value="string">String</option>
                            <option value="text">Text</option>
                            <option value="integer">Integer</option>
                            <option value="decimal">Decimal</option>
                            <option value="boolean">Boolean</option>
                            <option value="date">Date</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="nullable[]" class="form-label">Nullable:</label>
                        <select name="nullable[]" class="form-select">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger" onclick="removeField(this)">Remove</button>
                    </div>
                </div>
            </div>`;
        document.getElementById('fields').insertAdjacentHTML('beforeend', fieldTemplate);
    }

    function removeField(button) {
        button.parentElement.parentElement.parentElement.remove();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
