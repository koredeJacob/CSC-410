<?php
$db = new mysqli('localhost', 'root', '', 'csc410');

if ($db->connect_errno) {
    die('sorry we are experiencing some errors');
}

$routes = [
    '/' => 'home',
    '/upload' => 'uploadImage',
    '/getMovies' => 'getMovies',
    '/addComment' => 'addComment',
    '/getComments' => 'getComments'
];

function home()
{
    echo 'home page';
}

function uploadImage()
{
    global $db;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_FILES['file'])) {
            $filename = $_FILES["file"]["name"];
            $tempname = $_FILES["file"]["tmp_name"];
            $folder = "./images/" . $filename;
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $sql = "INSERT INTO image (filename , title) VALUES ('$filename','$title')";

            if (mysqli_query($db, $sql)) {
                http_response_code(201);
                echo "movie uploaded successfully";
            } else {
                echo "movie failed to upload";
            }

            if (move_uploaded_file($tempname, $folder)) {
                echo 'image uploaded successfully! ';
            } else {
                echo 'image failed to load';
            }
        }
    }
}

function getMovies()
{
    global $db;
    $sql = "SELECT * FROM image";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        header('Content-Type:application/json');
        echo json_encode($data);
    } else {
        echo "no data found";
    }
}

function addComment()
{
    global $db;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $post_data = file_get_contents('php://input');
        $params = json_decode($post_data, true);
        $movieid = '';
        $commentText = '';
        if (isset($params['movie'])) {
            $movieid = $params['movie'];
        }
        if (isset($params['comment'])) {
            $commentText = $params['comment'];
        }
        $sql = "INSERT INTO comments (movie,comment) VALUES ('$movieid','$commentText')";
        if (mysqli_query($db, $sql)) {
            http_response_code(201);
            echo 'comment added successfully';
        } else {
            echo 'failed to add comment';
        }
    }
}

function getComments()
{
    global $db;
    $sql = "SELECT * FROM comments";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        $data = $result->fetch_all(MYSQLI_ASSOC);
        header('Content-Type:application/json');
        echo json_encode($data);
    } else {
        echo "no data found";
    }
}

$request_uri = $_SERVER['REQUEST_URI'];
if (!array_key_exists($request_uri, $routes)) {
    http_response_code((404));
    echo "404 Not Found";
    exit;
}

$route_function = $routes[$request_uri];
$route_function();

$db->close();
