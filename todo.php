<?php
// Include the configuration file
require 'config.php';

// Function to read all ToDo items from the JSON file
function getTodos() {
    $todos = json_decode(file_get_contents(TODO_FILE), true);
    return is_array($todos) ? $todos : [];
}

// Function to save all ToDo items to the JSON file
function saveTodos($todos) {
    file_put_contents(TODO_FILE, json_encode($todos, JSON_PRETTY_PRINT));
}

// Function to add a new ToDo item
function addTodo($title, $duedate, $description = '') {
    $todos = getTodos();
    $newTodo = [
        'id' => uniqid(),
        'title' => $title,
        'duedate' => $duedate,
        'description' => $description,
        'created_at' => date('Y-m-d'),
        /*'created_at' => date('Y-m-d H:i:s'),*/
        'completed' => false
    ];
    $todos[] = $newTodo;
    saveTodos($todos);
    return $newTodo;
}

// Function to update an existing ToDo item by ID
function updateTodo($id, $title = null, $duedate = null, $description = null, $completed = null) {
    $todos = getTodos();
    foreach ($todos as &$todo) {
        if ($todo['id'] === $id) {
            if ($title !== null) $todo['title'] = $title;
            if ($duedate !== null) $todo['duedate'] = $duedate;
            if ($description !== null) $todo['description'] = $description;
            if ($completed !== null) $todo['completed'] = $completed;
            saveTodos($todos);
            return $todo;
        }
    }
    return null;
}

// Function to delete a ToDo item by ID
function deleteTodo($id) {
    $todos = getTodos();
    $todos = array_filter($todos, function($todo) use ($id) {
        return $todo['id'] !== $id;
    });
    saveTodos(array_values($todos));
    return true;
}

// Handle form submissions for adding, updating, or deleting ToDos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        addTodo($_POST['title'], $_POST['duedate'], $_POST['description']);
    } elseif (isset($_POST['update'])) {
        updateTodo($_POST['id'], $_POST['title'], $_POST['duedate'], $_POST['description'], isset($_POST['completed']) ? true : false);
    } elseif (isset($_POST['delete'])) {
        deleteTodo($_POST['id']);
    }
}

// Fetch all ToDo items
$todos = getTodos();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
    <!-- Bootstrap 5 CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">  -->   

    <!-- https://icons.getbootstrap.com/ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="quickadmin/bootstrap-5.3.3/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="quickadmin/css/bootstrap-datepicker-1.8.0.css"/>
    <link rel="stylesheet" href="quickadmin/css/reports/2.9.2-semantic.min.css"/>
    <link rel="stylesheet" href="quickadmin/css/reports/2.0.8-css-dataTables.semanticui.css"/>
    <link rel="stylesheet" href="quickadmin/css/reports/3.0.2-css-buttons.semanticui.css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.css"/>

    <style>
        .completed { text-decoration: line-through; }
        a, u {
          text-decoration: none;
          color: #a8a7a7;
          font-size: 18px;
        } /*Remove the underline for anchors-links*/

        a:hover {
          color: grey;
          font-size: 20px;
        }

        .scrollme {
            overflow-x: auto;
        }

    </style>

    <script language="JavaScript" type="text/javascript">
        function checkDelete(){
            return confirm('Are you sure?');
        }
    </script>

</head>
<body>
    <div class="container mt-4">

        <div class="text-center text-success"><b><a href="todo.php">ToDoList | Home</a></b></div><hr>

        <!-- Add New ToDo Form -->
        <!-- <h3>Add New ToDo</h3> -->
        <form method="POST">
            <div class="card shadow p-4 scrollme">
                <div class="row">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xlg-8 mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" placeholder="" required>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xlg-4 mb-3">
                        <label>Due Date</label>
                        <input type="date" name="duedate" class="form-control" placeholder="" required>
                    </div>
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" placeholder="" required></textarea>
                    </div>
                    <button type="submit" name="add" class="btn btn-success">SAVE</button>
                </div>
            </div>
        </form><hr>

        <!-- Display ToDo List -->
        <!-- <h3 class="mt-5">Your ToDo List</h3> -->
        <div class="card shadow p-4 scrollme">
            <table id="example" class="table table-striped table-bordered table-hover table-condensed table-responsive text-start dt-select" style="height: auto; width: 100%; font-size: 14px;">
                <thead>
                    <tr>
                        <!-- <th></th> -->
                        <th>Due Date</th>
                        <th>Done</th>
                        <th>Title | Description</th>
                        <th>Option</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todos as $todo): ?>
                        <tr>
                            <!-- Update Form -->
                            <form method="POST" style="display:inline;">
                                <!-- <td><input type="hidden" name="id" value="<?php echo $todo['id']; ?>"></td> -->
                                <td>
                                    <?php if ($todo['completed']) { ?>
                                            <input type="date" name="duedate" class="form-control" value="<?php echo ($todo['duedate']); ?>" readonly>
                                    <?php    } else { ?>
                                            <input type="date" name="duedate" class="form-control" value="<?php echo ($todo['duedate']); ?>" required>
                                    <?php    }
                                    ?>
                                </td>
                                <td><div class="form-control container"><?php echo $todo['completed'] ? 'Yes' : 'No'; ?>
                                    <input type="hidden" name="id" value="<?php echo $todo['id']; ?>">
                                    <input class="float-end" type="checkbox" name="completed" <?php echo $todo['completed'] ? 'checked' : ''; ?> style="height: 20px;"></div>
                                </td>
                                <td>
                                    <?php if ($todo['completed']) { ?>
                                        <textarea rows="1" cols="100" style="max-width:100%;" type="text" name="title" class="form-control" readonly><?php echo htmlspecialchars($todo['title']); ?></textarea>
                                    <?php    } else { ?>
                                        <!-- <input type="hidden" name="id" value="<?php echo $todo['id']; ?>"> -->
                                        <textarea rows="1" cols="100" style="max-width:100%;" type="text" name="title" class="form-control"><?php echo htmlspecialchars($todo['title']); ?></textarea>
                                    <?php    }
                                    ?>

                                    <?php if ($todo['completed']) { ?>
                                            <textarea rows="3" cols="30" style="max-width:100%;" name="description" class="form-control" readonly><?php echo htmlspecialchars($todo['description']); ?></textarea>
                                    <?php    } else { ?>
                                            <textarea rows="3" cols="30" style="max-width:100%;" name="description" class="form-control"><?php echo htmlspecialchars($todo['description']); ?></textarea>
                                    <?php    }
                                    ?>
                                </td>
                                <td>
                                    <button type="submit" name="update" class="btn btn-sm btn-success mt-2"><i class="bi bi-pencil-square"></i></button>

                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-sm btn-info mt-2" data-bs-toggle="modal" data-bs-target="#todoModal<?php echo $todo['id']; ?>"><i class="bi bi-pencil-square"></i>
                                    </button>
                                </td>
                            </form>

                            <form method="POST" style="display:inline;">
                                <!-- Modal -->
                                <div class="modal fade" id="todoModal<?php echo $todo['id']; ?>" tabindex="-1" aria-labelledby="todoModalLabel<?php echo $todo['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="todoModalLabel<?php echo $todo['id']; ?>">Edit Record</h1>
                                                <button type="button" class="btn btn-sm btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                <?php if ($todo['completed']) { ?>
                                                    <label>Due Date</label>
                                                    <input type="date" name="duedate" class="form-control" value="<?php echo ($todo['duedate']); ?>" readonly>
                                                <?php    } else { ?>
                                                    <label>Due Date</label>
                                                    <input type="date" name="duedate" class="form-control" value="<?php echo ($todo['duedate']); ?>" required>
                                                <?php    }
                                                ?>
                                                </div>
                                                <div class=" mb-3">
                                                <?php if ($todo['completed']) { ?>
                                                    <label>Title</label>
                                                    <textarea rows="2" cols="100" style="max-width:100%;" type="text" name="title" class="form-control" readonly><?php echo htmlspecialchars($todo['title']); ?></textarea>
                                                <?php    } else { ?>
                                                    <label>Title</label>
                                                    <textarea rows="2" cols="100" style="max-width:100%;" type="text" name="title" class="form-control"><?php echo htmlspecialchars($todo['title']); ?></textarea>
                                                <?php    }
                                                ?>
                                                </div>
                                                <div class="mb-3">
                                                <?php if ($todo['completed']) { ?>
                                                    <label>Description</label>
                                                    <textarea rows="3" cols="30" style="max-width:100%;" name="description" class="form-control" readonly><?php echo htmlspecialchars($todo['description']); ?></textarea>
                                                <?php    } else { ?>
                                                    <label>Description</label>
                                                    <textarea rows="3" cols="30" style="max-width:100%;" name="description" class="form-control"><?php echo htmlspecialchars($todo['description']); ?></textarea>
                                                <?php    }
                                                ?>
                                                </div>
                                                <div class="mb-3">
                                                    <label>
                                                        <input type="hidden" name="id" value="<?php echo $todo['created_at']; ?>">
                                                        <input type="checkbox" name="completed" <?php echo $todo['completed'] ? 'checked' : ''; ?>> Completed
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- <div class="modal-footer"> -->
                                                <div class="float-start p-2">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="update" class="btn btn-sm btn-primary">Update</button>
                                                </div>
                                                <!-- Delete Form -->
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="id" value="<?php echo $todo['id']; ?>">
                                                    <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return checkDelete()"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap 5 JS (Optional for functionality like dropdowns, modals, etc.) -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
