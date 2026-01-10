<?php
include 'db_connection.php';

// Create tables if not exist
$tables = [
    "colleges" => "CREATE TABLE IF NOT EXISTS colleges (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        contact VARCHAR(20),
        about TEXT,
        active TINYINT(1) DEFAULT 1
    )",
    "departments" => "CREATE TABLE IF NOT EXISTS departments (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        head VARCHAR(255),
        contact VARCHAR(20),
        active TINYINT(1) DEFAULT 1
    )",
    "coordinators" => "CREATE TABLE IF NOT EXISTS coordinators (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        contact VARCHAR(20),
        department VARCHAR(50),
        active TINYINT(1) DEFAULT 1
    )",
    "faculty" => "CREATE TABLE IF NOT EXISTS faculty (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        contact VARCHAR(20),
        department VARCHAR(50),
        qualification VARCHAR(255),
        experience INT,
        specialization VARCHAR(255),
        active TINYINT(1) DEFAULT 1
    )"
];

foreach ($tables as $table => $sql) {
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table $table: " . mysqli_error($conn));
    }
}

// Handle POST requests
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_college'])) {
        $id = $_POST['college_id'];
        $name = $_POST['college_name'];
        $email = $_POST['college_email'];
        $password = $_POST['college_password'];
        $contact = $_POST['college_contact'];
        $about = $_POST['college_about'];
        $sql = "INSERT INTO colleges (id, name, email, password, contact, about) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssss', $id, $name, $email, $password, $contact, $about);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'College added successfully!';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['add_department'])) {
        $id = $_POST['department_id'];
        $name = $_POST['department_name'];
        $description = $_POST['department_description'];
        $head = $_POST['department_head'];
        $contact = $_POST['department_contact'];
        $sql = "INSERT INTO departments (id, name, description, head, contact) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssss', $id, $name, $description, $head, $contact);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Department added successfully!';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['add_coordinator'])) {
        $id = $_POST['coordinator_id'];
        $name = $_POST['coordinator_name'];
        $email = $_POST['coordinator_email'];
        $password = $_POST['coordinator_password'];
        $contact = $_POST['coordinator_contact'];
        $department = $_POST['coordinator_department'];
        $sql = "INSERT INTO coordinators (id, name, email, password, contact, department) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssss', $id, $name, $email, $password, $contact, $department);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Coordinator added successfully!';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['add_faculty'])) {
        $id = $_POST['faculty_id'];
        $name = $_POST['faculty_name'];
        $email = $_POST['faculty_email'];
        $contact = $_POST['faculty_contact'];
        $department = $_POST['faculty_department'];
        $qualification = $_POST['faculty_qualification'];
        $experience = $_POST['faculty_experience'];
        $specialization = $_POST['faculty_specialization'];
        $sql = "INSERT INTO faculty (id, name, email, contact, department, qualification, experience, specialization) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssssis', $id, $name, $email, $contact, $department, $qualification, $experience, $specialization);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Faculty added successfully!';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } elseif (isset($_POST['deactivate'])) {
        $table = $_POST['table'];
        $id = $_POST['id'];
        $sql = "UPDATE $table SET active = 0 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Deactivated successfully!';
        } else {
            $message = 'Error: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch data
$colleges = mysqli_query($conn, "SELECT * FROM colleges WHERE active = 1");
$departments = mysqli_query($conn, "SELECT * FROM departments WHERE active = 1");
$coordinators = mysqli_query($conn, "SELECT * FROM coordinators WHERE active = 1");
$faculty = mysqli_query($conn, "SELECT * FROM faculty WHERE active = 1");

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Entry - IQAC Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">IQAC Data Entry System</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Colleges -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Add College</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>College ID</label>
                            <input type="text" name="college_id" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>College Name</label>
                            <input type="text" name="college_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="college_email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="college_password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Contact</label>
                            <input type="text" name="college_contact" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>About</label>
                            <textarea name="college_about" class="form-control"></textarea>
                        </div>
                    </div>
                    <button type="submit" name="add_college" class="btn btn-primary">Add College</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Colleges</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>About</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($colleges)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['contact']; ?></td>
                                <td><?php echo $row['about']; ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="table" value="colleges">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="deactivate" class="btn btn-danger btn-sm">Deactivate</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Departments -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Add Department</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Department ID</label>
                            <input type="text" name="department_id" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Department Name</label>
                            <input type="text" name="department_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Description</label>
                            <textarea name="department_description" class="form-control" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Head</label>
                            <input type="text" name="department_head" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Contact</label>
                            <input type="text" name="department_contact" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" name="add_department" class="btn btn-primary">Add Department</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Departments</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Head</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php mysqli_data_seek($departments, 0); while ($row = mysqli_fetch_assoc($departments)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['head']; ?></td>
                                <td><?php echo $row['contact']; ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="table" value="departments">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="deactivate" class="btn btn-danger btn-sm">Deactivate</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Coordinators -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Add Coordinator</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Coordinator ID</label>
                            <input type="text" name="coordinator_id" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" name="coordinator_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="coordinator_email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="coordinator_password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Contact</label>
                            <input type="text" name="coordinator_contact" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Department</label>
                            <select name="coordinator_department" class="form-control" required>
                                <option value="">Select Department</option>
                                <?php mysqli_data_seek($departments, 0); while ($dept = mysqli_fetch_assoc($departments)): ?>
                                    <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add_coordinator" class="btn btn-primary">Add Coordinator</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Coordinators</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Department</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($coordinators)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['contact']; ?></td>
                                <td><?php echo $row['department']; ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="table" value="coordinators">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="deactivate" class="btn btn-danger btn-sm">Deactivate</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Faculty -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Add Faculty</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Faculty ID</label>
                            <input type="text" name="faculty_id" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" name="faculty_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="faculty_email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Contact</label>
                            <input type="text" name="faculty_contact" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Department</label>
                            <select name="faculty_department" class="form-control" required>
                                <option value="">Select Department</option>
                                <?php mysqli_data_seek($departments, 0); while ($dept = mysqli_fetch_assoc($departments)): ?>
                                    <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Qualification</label>
                            <input type="text" name="faculty_qualification" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Experience (years)</label>
                            <input type="number" name="faculty_experience" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Specialization</label>
                            <input type="text" name="faculty_specialization" class="form-control">
                        </div>
                    </div>
                    <button type="submit" name="add_faculty" class="btn btn-primary">Add Faculty</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h3>Faculty</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Department</th>
                            <th>Qualification</th>
                            <th>Experience</th>
                            <th>Specialization</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($faculty)): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['contact']; ?></td>
                                <td><?php echo $row['department']; ?></td>
                                <td><?php echo $row['qualification']; ?></td>
                                <td><?php echo $row['experience']; ?></td>
                                <td><?php echo $row['specialization']; ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="table" value="faculty">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="deactivate" class="btn btn-danger btn-sm">Deactivate</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>