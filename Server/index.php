<?php
$db = new mysqli('localhost', 'root', '', 'csc410');

if ($db->connect_errno) {
    die('sorry we are experiencing some errors');
}

if (isset($_POST['upload'])) {
    $filename = $_FILES["uploadfile"]["name"];
    $tempname = $_FILES["uploadfile"]["tmp_name"];
    $folder = "./image/" . $filename;

    $sql = "INSERT INTO image (filename) VALUES ('$filename')";

    mysqli_query($db, $sql);

    if (move_uploaded_file($tempname, $folder)) {
        echo '<h3> image uploaded successfully! </h3';
    } else {
        echo 'image failed to load';
    }
}

function addComment($movieid, $commentText)
{
    global $db;
    $sql = "INSERT INTO comments (movie,comment) VALUES ('$movieid','$commentText')";
    if ($db->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function getComments($movieid)
{
    global $db;
    $sql = "SELECT * FROM comments WHERE movie = '$movieid' ";
    $result = $db->query($sql);
    $comments = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row['comment'];
        }
    }
    return $comments;
}

$db->close();
