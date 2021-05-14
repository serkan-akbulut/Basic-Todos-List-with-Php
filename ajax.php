<?php
session_start();
if (!isset($_SESSION["user"])) {
    $data['status'] = 0;
    $data['message'] = "You do not have permission, please login";
    exit;
} else {
    $user = $_SESSION["user"];
    $user_id = $user["id"];
}
include("db.php");
header('Content-Type: application/json'); //this page will return json

if ($_POST['do'] == "addlist") { //add list
    if ((!$_POST['name']) || ctype_space($_POST['name'])) {
        $data['status'] = 0;
        $data['name'] = $_POST['name'];
        $data['message'] = "Registration failed list name cannot be blank";
    } else {
        $sql = "INSERT INTO lists (name,user_id) VALUES (?,?)";
        $prepare = $db->prepare($sql);
        $add = $prepare->execute([$_POST['name'], $user['id']]);
        if ($add) {
            $data['status'] = 1;
            $data['name'] = $_POST['name'];
            $data['list_id'] = $db->lastInsertId();
            $data['message'] = "Registration Successful";
        } else {
            $data['status'] = 0;
            $data['name'] = $_POST['name'];
            $data['message'] = "Registration Unsuccessful";
        }
    }
}
if ($_POST['do'] == "addtodo") { //todo ekle
    if ((!$_POST['title']) || ctype_space($_POST['title'])) {
        $data['status'] = 0;
        $data['title'] = $_POST['title'];
        $data['message'] = "Registration failed list name cannot be blank";
    } else {
        $sql = "INSERT INTO todos (title,user_id,list_id) VALUES (?,?,?)";
        $prepare = $db->prepare($sql);
        $add = $prepare->execute([$_POST['title'], $user['id'], $_POST['list_id']]);
        if ($add) {
            $data['status'] = 1;
            $data['title'] = $_POST['title'];
            $data['message'] = "Registration Successful";
        } else {
            $data['status'] = 0;
            $data['title'] = $_POST['title'];
            $data['message'] = "Registration Unsuccessful";
        }
    }
}
if ($_POST['do'] == "completask") { //todo complete
    $id = $_POST['todo_id'];
    $query = $db->query("select list_id from todos where id='$id'")->fetch();
    $list_id = $query['list_id'];
    $query = $db->query("select * from completed_todos where todos_id='$id'")->fetch();
    if ($query) {
        $delete = $db->exec("delete from completed_todos where todos_id='$id'");
        $data['status'] = 0;
        $data['message'] = "The task is deleted from completed tasks list.";
    } else {
        $sql = "INSERT INTO completed_todos (todos_id,user_id,list_id) VALUES (?,?,?)";
        $prepare = $db->prepare($sql);
        $add = $prepare->execute([$id, $user['id'], $list_id]);
        $data['status'] = 1;
        $data['message'] = "The task is added to completed tasks list.";
    }
}
if ($_POST['do'] == "favoritetodo") { //todo complete
    $id = $_POST['todo_id'];
    $task=$db->query("select * from todos where id='$id'")->fetch();
    $list_id=$task['list_id'];
    $query = $db->query("select * from important_todos where todos_id='$id'")->fetch();
    if ($query) {
        $delete = $db->exec("delete from important_todos where todos_id='$id'");
        $data['status'] = 0;
        $data['message'] = "The task is deleted from important tasks list.";
    } else {
        $sql = "INSERT INTO important_todos (todos_id,user_id,list_id) VALUES (?,?,?)";
        $prepare = $db->prepare($sql);
        $add = $prepare->execute([$id,$user['id'],$list_id]);
        $data['status'] = 1;
        $data['message'] = "The task is added to important tasks list.";
    }
}
if ($_POST['do'] == "deletetodo") { //todo delete
    $id = $_POST['todo_id'];
    $db->exec("delete from todos where id='$id'");
    $db->exec("delete from completed_todos where todos_id='$id'");
    $db->exec("delete from important_todos where todos_id='$id'");
    $data['status'] = 1;
    $data['message'] = "The task is deleted.";
}
if ($_POST['do'] == "deletelist") { //delete list
    $id = $_POST['list_id'];
    $db->exec("delete from lists where id='$id'");
    $db->exec("delete from todos where list_id='$id'");
    $db->exec("delete from completed_todos where list_id='$id'");
    $db->exec("delete from important_todos where list_id='$id'");
    $data['status'] = 1;
    $data['message'] = "The list has been deleted with the related tasks.";
}
if ($_POST['do'] == "getlist") { //current list
    $list = $db->query("select * from lists where user_id='$user_id'")->fetchAll();
    foreach ($list as $key => $item) {
        $list_id = $item['id'];
        $query = $db->query("select * from completed_todos where list_id='$list_id'")->fetchAll();
        $count = count($db->query("select * from todos where list_id='$list_id'")->fetchAll()) - count($query);
        if($count==0){
            $list[$key]['count'] ="";
        }else{
            $list[$key]['count'] = $count;
        }
        
    }
    $data["list"] = $list;
}
if ($_POST['do'] == "gettodos") { //current todos
    $list_id = $_POST['list_id'];
    $todos = $db->query("select * from todos where user_id='$user_id' and list_id='$list_id'")->fetchAll();
    foreach ($todos as $key => $item) {
        extract($item);
        $query = $db->query("select * from completed_todos where todos_id='$id'")->fetch();
        if ($query) {
            $todos[$key]['completed'] = 1;
        }
        $query = $db->query("select * from important_todos where todos_id='$id'")->fetch();
        if ($query) {
            $todos[$key]['important'] = 1;
        }
    }
    $data["list"] = $todos;
}
if ($_POST['do'] == "getimportance") { //current todos 
    $importants = $db->query("select * from important_todos where user_id='$user_id'")->fetchAll();
    foreach ($importants as $key => $item) {
        extract($item);
        $query = $db->query("select * from completed_todos where todos_id='$todos_id'")->fetch();
        if ($query) {
            unset($importants[$key]);
        } else {
            $task = $db->query("select * from todos where id='$todos_id'")->fetch();
            if ($task) {
                $list_id = $task['list_id'];
                $importants[$key]['title'] = $task['title'];
                $list = $db->query("select * from lists where id='$list_id'")->fetch();
                if ($list) {
                    $importants[$key]['list_name'] = $list['name'];
                }
            }
        }
    }
    $data["list"] = $importants;
}



echo json_encode($data);
