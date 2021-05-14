<?php
session_start();
require_once "./protect.php";
include "db.php";

$user = $_SESSION["user"];
// var_dump($user) ;

if (!empty($_POST)) {

  $todoAdd = $_POST['todoAdd'];

  echo "<script>";
  if (empty($todoAdd)) {
    echo "M.toast({html: 'empty list'}); ";
  }
  echo "</script>";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Title of the document</title>
  <script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

  <style>
    .container {
      margin-top: 100px;
    }

    .circle {
      vertical-align: middle;
    }

    #div_top_hypers {

      display: inline;

      background-color: aqua;

    }

    #ul_top_hypers li {
      display: inline;
      padding-left: 40px;
    }


    body,
    html {
      height: 100%;
    }

    #full-height {
      height: 100%;
    }

    tr:hover {
      background-color: rgb(240, 240, 240);
    }

 
  </style>
</head>

<body>

  <!-- Navbar goes here -->

  <!-- Page Layout here -->
  <div class="row">

    <div id="full-height" class="col s3 m3 l3 ">
      <!-- Note that "m4 l3" was added -->
      <div style="background-color: white; height:100vh">

        <div id="div_top_hypers">
          <ul id="ul_top_hypers">
            <li>
               <!-- ÖRNEK KODLARINIZI İNCELEYEREK  EK OLARAK UPDATE PROFILE EKLEDİK HOCAM.  -->
              <a href="update_profile.php" class="left">
                <?php
                $profile = $user["profile"] ?? "avatar.png";
                echo "<img src='images/$profile' width='50' class='circle' > ";

                ?>
              </a>
            </li>
            <li> <a href=""> </a>
              <?php

              echo $user["name"];

              ?>
            </li>
            <li> <a href=""> </a>
              <?php
              echo $user["email"];
              ?>
            </li>
            <li><a href="logout.php" style="color: green;"> <i class="material-icons">input</i></a></li>

        </div>
        <table>
          <tr>

            <td>
              <i class="material-icons small">star_border</i>
            </td>
            <td>
              <h6> <a style="cursor: pointer;" onclick="getImportance();">Importance</a></h6>
            </td>

          </tr>
          <tbody id="list">
            <?php
            $user_id = $user['id'];
            
            $lists = $db->query("select * from lists where user_id='$user_id'")->fetchAll(); //get list
            if ($lists) {
              $list_id = $lists[0]['id']; //get first list items' id
              if (isset($_GET['list_id'])) { //if isset get list id
                $list_id=$_GET['list_id'];
                $list=$db->query("select * from lists where id='$list_id' and user_id='$user_id'")->fetch();
                $todos = $db->query("select * from todos where list_id='$list_id' and user_id='$user_id'")->fetchAll();  
              } else {
                $list=$lists[0];
                $todos = $db->query("select * from todos where list_id='$list_id' and user_id='$user_id'")->fetchAll(); //get todos list's first item's
              } 
              foreach ($lists as $item) {
                $listid = $item['id'];
                $query = $db->query("select * from completed_todos where list_id='$listid'")->fetchAll();
                $count = count($db->query("select * from todos where list_id='$listid'")->fetchAll()) - count($query);
                if($count==0){
                  $count ="";
              }else{
                  $count = $count;
              }

            ?>
                <tr id="list<?= $item['id']; ?>">
                  <td><i class="material-icons small">menu</i></td>
                  <td><a href="main.php?list_id=<?= $item['id']; ?>"><?= $item['name']; ?></a></td>
                  <td>  <span class="badge"><?= $count; ?></span>   </td>
                  <td><a style="cursor:pointer;" onclick="deleteList(<?= $item['id']; ?>)"><i class="material-icons small">delete</i></a></td>
                </tr>
            <?php
              }
            }
            ?>
          </tbody>

          <tr>

            <td>

              <i class="material-icons small">add</i>
            </td>
            <td>
              <h6> <a class=" modal-trigger" href="#modal1">New List</a></h6>
            </td>

          </tr>



        </table>



      </div>

    </div>




    <div class="col s9 m9 l9">
      <div style="background-color: rgb(115,179,238); height:100vh;">
        <?php if ($lists) { ?>
          <h1 id="listName" style="color: white;padding-left:50px"><?= $list['name']; ?></h1>
          <ul class="collection" id="todos">
            <?php if ($todos) { ?>
              <?php foreach ($todos as $item) {
                $todos_id = $item['id'];
                $query = $db->query("select * from important_todos where todos_id='$todos_id'")->fetch();
                if ($query) {
                  $star = "grade";
                } else {
                  $star = "star_border";
                }
                $query = $db->query("select * from completed_todos where todos_id='$todos_id'")->fetch();
                if ($query) {
                  $checked = "checked";
                  $style = "text-decoration: line-through;";
                } else {
                  $checked = "";
                  $style = "";
                }
              ?>
                
                <li class="collection-item" id="task<?= $item['id']; ?>">
                  <label>
                    <input  onchange="completeTask(<?= $item['id']; ?>);" type="checkbox" <?= $checked; ?> />
                    <span></span>
                  </label>
                  <span class="title" id="tasktitle<?= $item['id']; ?>" style="<?= $style; ?>"><?= $item['title']; ?></span>
                  <a style="cursor: pointer;" class="secondary-content" onclick="deleteTodo(this,<?= $item['id']; ?>)"><i class="material-icons">delete</i></a>
                  <a style="cursor: pointer;" class="secondary-content" onclick="favoriteTodo(this,<?= $item['id']; ?>)"><i id="favIcon<?= $item['id']; ?>" class="material-icons"><?= $star; ?></i></a>
                </li>
              
              <?php } ?>
            <?php } ?>
          </ul>

        <?php } ?>

        </form>
        <div style="position: fixed; border-radius: 15px; border: 1px solid; bottom:20px; background-color:rgb(109,123,189); height:100px; width:50%; margin-left:10%; margin-right:1%;">
          <form id="addTodo" action="#" method="post">

            <div class="input-field" >
              <input style="color: #eee;" name="todoAdd" id="todoTitle" type="text" class="validate" value="<?= isset($todoAdd) ? $todoAdd : "" ?>">
              <label  for="todoTitle"><i class="material-icons small">add</i>  Add a Task</label>
            </div>
            <input type="hidden" id="list_id" value="<?= $list_id; ?>">
        </div>

      </div>

    </div>


    <!-- Modal Structure -->
    <div id="modal1" class="modal">
      <div class="modal-content">
        <form id="addForm" action="#" method="post">
          <div class="input-field">
            <input name="listAdd" id="listAdd" type="text" class="validate" value="<?= isset($todoAdd) ? $todoAdd : "" ?>">
            <label for="listAdd">List Name</label>
          </div>
        </form>
      </div>

    </div>


    <script>
      var listAdd;
      var getList;
      var getImportance;
      var todoAdd;
      var deleteTodo;
      var deleteList;
      var favoriteTodo; 
      var completeTask;
      var completeCheck;
      var listid;
      $(document).ready(function() {
        deleteTodo = function(elem, todo_id) { //delete todo 
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "deletetodo",
              todo_id: todo_id
            },
            success: function(data) {
              M.toast({
                html: data.message
              })
              if (data.status == 1) {
                $("#task" + todo_id).remove();
                getList();
              }
              console.log(data);
            },
            dataType: 'json'
          });
        }
        deleteList = function(list_id) { //delete todo
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "deletelist",
              list_id: list_id
            },
            success: function(data) {
              M.toast({
                html: data.message
              })
              if (data.status == 1) {
                $("#list" + list_id).remove();
              }
              console.log(data);
            },
            dataType: 'json'
          });
        }
        favoriteTodo = function(elem, todo_id) { //add favorite 
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "favoritetodo",
              todo_id: todo_id
            },
            success: function(data) {
              M.toast({
                html: data.message
              })
              if (data.status == 1) {
                $("#favIcon" + todo_id).html("grade");
              } else {
                $("#favIcon" + todo_id).html("star_border");
              }

              console.log(data);
            },
            dataType: 'json'
          });
        }
        completeTask = function(todo_id) { //complete todo
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "completask",
              todo_id: todo_id
            },
            success: function(data) {
              M.toast({
                html: data.message
              })
              if (data.status == 1) {
                $("#tasktitle" + todo_id).css("text-decoration", "line-through");
              } else {
                $("#tasktitle" + todo_id).css("text-decoration", "none");
              }
              getList();
              console.log(data);
            },
            dataType: 'json'
          });
        }
        getList = function() { //get current list
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "getlist"
            },
            success: function(data) {
              console.log(data);
              $("#list").html("");
              $.each(data.list, function(i, item) {
                $("#list").append(`
              <tr id="list` + item.id + `">
              <td><i class="material-icons small">menu</i></td>
              <td><a href="main.php?list_id=` + item.id + `">` + item.name + `</a></td>
              <td>` + item.count + `</td>
              <td><a style="cursor:pointer;" onclick="deleteList(` + item.id + `)"><i class="material-icons small">delete</i></a></td>
              </tr>
              `);
              });


            },
            dataType: 'json'
          });
        }
        getTodos = function(list_id) { //get todos
          
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "gettodos",
              list_id: list_id
            },
            success: function(data) {
              $("#todos").html("");
              $.each(data.list, function(i, item) {
                var checked = "";
                var star = "";
                var text_decoration = "";
                if (item.completed == 1) {
                  checked = "checked";
                  text_decoration = "text-decoration: line-through;";
                }
                if (item.important == 1) {
                  star = "grade";
                } else {
                  star = "star_border";
                }
                $("#todos").append(`
              <li class="collection-item" id="task` + item.id + `">
              <label>
                <input onchange="completeTask(` + item.id + `);" type="checkbox" ` + checked + `/>
                <span></span>
              </label>
              <span class="title"  id="tasktitle` + item.id + `" style="` + text_decoration + `">` + item.title + `</span> 
              <a style="cursor: pointer;" class="secondary-content" onclick="deleteTodo(this,` + item.id + `)"><i class="material-icons">delete</i></a>
              <a style="cursor: pointer;" class="secondary-content" onclick="favoriteTodo(this,` + item.id + `)"><i id="favIcon` + item.id + `" class="material-icons">` + star + `</i></a>
              </li>
              `);
              });

              console.log(data);
            },
            dataType: 'json'
          });
        }
        getImportance = function() { //get importants
          
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "getimportance"
            },
            success: function(data) {
              $("#todos").html("");
              $.each(data.list, function(i, item) { 
                $("#todos").append(`
              <li class="collection-item"> <i class="material-icons small">star_border</i>
 <h7>`+item.title+`</h7><br>
 (`+item.list_name+`)
              </li>
              `);
              });

              console.log(data);
            },
            dataType: 'json'
          });
        }
        listAdd = function(name) { //Add List
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "addlist",
              name: name
            },
            success: function(data) {
              if (data.status == 1) {
                getList();
                $("#listName").html(data.name);
                $("#list_id").val(data.list_id);
              } else {
                M.toast({
                  html: data.message
                })
              }
            },
            dataType: 'json'
          });
        }
        todoAdd = function(title, list_id) { //Add Todo
          $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: {
              do: "addtodo",
              title: title,
              list_id: list_id
            },
            success: function(data) {
              if (data.status == 1) {
                getList();
                getTodos(list_id);
              } else {
                M.toast({
                  html: data.message
                })
              }
              console.log(data);
            },
            dataType: 'json'
          });
        }

        $("#listAdd").keypress(function(event) {
          if (event.keyCode === 13) {
            listAdd($("#listAdd").val());
            
            return false;
          }
        });
        $("#addTodo").keypress(function(event) {
          if (event.keyCode === 13) {
            todoAdd($("#todoTitle").val(), $("#list_id").val());

            return false;
          }
        });

        
        $('.modal').modal({});
      });
    </script>
</body>

</html>