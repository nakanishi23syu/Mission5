<!DOCTYPE html>
<html lang = "ja">
    <head>
        <meta charset="UTF-8">
        <title>m5-01</title>
    </head>
    <body>
        <?php
       


        session_start();//二重送信処理
        


        //プログラムの内容

        //1.データベース・テーブルの構築
            //1-1.データベースに接続
            //1-2.テーブルを作成する

        //2.フォームからname,comment,psを受信する
        
        //3.データベースにid,name,comment,date,psを送る
            //3-1 新規投稿
            //3-2 編集

        //4.削除機能を追加する
            //4-1.本物のps($d_ps_2)を取得する
            //4-2.本物のps($d_ps_2)と、入力したps($d_ps)が一致していたら削除する。

        //5.投稿フォームに編集内容したい内容をコピーする機能を追加する
            //5-1.本物のps($e_ps_2)を取得する
            //5-2.本物のps($e_ps_2)と、入力したps($e_ps)が一致していたら、投稿フォームに編集したい内容をコピーする

        //6.フォームを作る

        //7.実行内容を表示させる

        //8.データベースからレコードを受信する

        //9.受信したものからid,name,comment,dateを表示させる




        //後の変数未定義エラーを防ぐため「空白」を入れておく
        $E_name = "";
        $E_comment  = "";
        $edit ="";
        $message = "";




        //1.データベース・テーブルの構築
        //1-1.データベースに接続
        $dsn = "mysql:dbname=データベース名;host=********";
        $user = "ユーザー名";
        $password = "パスワード";
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

        //1-2.テーブルを作成する
        $sql = "CREATE TABLE IF NOT EXISTS m5_01_nakanishi"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "date DATETIME,"
        . "ps char(32)"
        .");";
        $stmt = $pdo->query($sql);


            /*デバックコード

            //1-3.デバック　テーブルの表示
            $sql ='SHOW TABLES';
            $result = $pdo -> query($sql);
            foreach ($result as $row){
                echo $row[0];
                echo '<br>';
            }
            echo "<hr>";

            //1-4.デバック　テーブルのカラム名の表示
            $sql ='SHOW CREATE TABLE m5_01_nakanishi';
            $result = $pdo -> query($sql);
            foreach ($result as $row){
                echo $row[1];
            }
            echo "<hr>";

            */
        if (isset($_REQUEST["chkno"]) && isset($_SESSION["chkno"]) 
        && ($_REQUEST["chkno"] == $_SESSION["chkno"])){//二重送信処理

            //2.フォームからname,comment,psを受信する
            if(isset($_POST["name"],$_POST["comment"],$_POST["ps"],$_POST["re_num"])){
                
                $name=$_POST["name"];
                $comment=$_POST["comment"];
                $date = date("Y-m-d  H:i:s");
                $ps = $_POST["ps"];
                $re_num=$_POST["re_num"];



                //3.データベースにid,name,comment,date,psを送る

                //3-1 新規投稿
                if($name !="" && $comment !="" && $ps !="" && $re_num ==""){
                    $sql = $pdo ->prepare("INSERT INTO m5_01_nakanishi (name, comment, date, ps)VALUES (:name, :comment, :date, :ps)");
                    $sql ->bindParam(':name', $name, PDO::PARAM_STR);
                    $sql ->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql ->bindParam(':date', $date, PDO::PARAM_STR);
                    $sql ->bindParam(':ps', $ps, PDO::PARAM_STR);
                    
                    $sql -> execute();
                    $message = "新規投稿完了";


                ////3-2 編集
                }elseif($name !="" && $comment !="" && $ps !="" && $re_num !=""){
                    $sql = "UPDATE m5_01_nakanishi SET name=:name,comment=:comment WHERE id=:id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $re_num, PDO::PARAM_INT);

                    $stmt->execute();
                    $message="編集完了";

                }
        
            }




            //4.削除機能を追加する
            //4-1.本物のps($d_ps_2)を取得する
            if(isset($_POST["delete"],$_POST["d_ps"])){
                $delete=$_POST["delete"];
                $d_ps=$_POST["d_ps"];
                
                $sql = "SELECT ps FROM m5_01_nakanishi WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $delete, PDO::PARAM_INT);
                $stmt->execute();

                $d_ps_2 = $stmt->fetch();

                //4-2.本物のps($d_ps_2)と、入力したps($d_ps)が一致していたら削除する。
                if($d_ps_2["ps"]  == $d_ps){
                    $sql = "delete from m5_01_nakanishi where id=:delete";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":delete", $delete, PDO::PARAM_INT);
                    $stmt->execute();

                    $message = "削除完了";
                }
            } 




            //5.投稿フォームに編集内容したい内容をコピーする機能を追加する
            //5-1.本物のps($e_ps_2)を取得する
            if(isset($_POST["E_num"],$_POST["e_ps"])){
                $edit=$_POST["E_num"];
                $e_ps=$_POST["e_ps"];

                $sql = "SELECT ps FROM m5_01_nakanishi WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":id", $edit, PDO::PARAM_INT);
                $stmt->execute();

                $e_ps_2 = $stmt->fetch();


                //5-2.本物のps($e_ps_2)と、入力したps($e_ps)が一致していたら、投稿フォームに編集したい内容をコピーする
                if($e_ps_2["ps"]  == $e_ps){
                    $sql = "SELECT name, comment FROM m5_01_nakanishi WHERE id = :edit";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":edit", $edit, PDO::PARAM_INT);
                    $stmt->execute();

                    $results = $stmt->fetchAll();
                    foreach ($results as $row){
                        $E_name = $row["name"];
                        $E_comment = $row["comment"];

                    }
                    $message = "投稿フォームに編集内容をコピーしました";
                                
                
                }

            }
        }
        $_SESSION["chkno"] = $chkno = mt_rand();//二重送信処理
        
        ?>


        

        <!--6.フォームを作る-->
        <!--名前・コメントを入力するフォーム-->
            
        <h>【投稿フォーム】</h>
        <form action="" method="post">
            <input type="hidden" name="chkno" value="<?php echo $chkno?>"><!--二重送信処理-->
            名前：<input type="text" name="name" value="<?php echo $E_name;?>"placeholder="名前"><br>
            コメント：<input type="text" name="comment" value="<?php echo $E_comment;?>"placeholder="コメント"><br>
            パスワード：<input type="password" name="ps" placeholder="パスワード"><br>
            <input hidden="text" name="re_num" value="<?php echo $edit;?>">
            <input type="submit" name="submit"><br><br>
        </form>


        <!--削除番号を入力するフォーム-->
        【削除フォーム】
        <form action="" method="post">
            <input type="hidden" name="chkno" value="<?php echo $chkno?>"><!--二重送信処理-->
            投稿番号：<input type="number" name="delete" placeholder="削除番号"><br>
            パスワード：<input type="password" name="d_ps" placeholder="パスワード"><br>
            <input type="submit" name="submit" value="削除"><br><br>
        </form>


        <!--編集番号を入力するフォーム-->
        【編集フォーム】
        <form action="" method="post">
            <input type="hidden" name="chkno" value="<?php echo $chkno?>"><!--二重送信処理-->
            投稿番号：<input type="number" name="E_num" placeholder="編集番号"><br>
            パスワード：<input type="password" name="e_ps" placeholder="パスワード"><br>
            <input type="submit" name="submit" value="編集"><br><br>
        </form>
        

        <!--7.実行内容を表示させる-->
        <br>
        -----------------------------------------<br>
        <?php echo $message;?><br>
        -----------------------------------------<br>



        <?php
        //8.データベースからレコードを受信する
        $sql = "SELECT * FROM m5_01_nakanishi";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();

        //9.受信したものからid,name,comment,dateを表示させる
        foreach ($results as $row){
            echo $row["id"]." ";
            echo $row["name"]." ";
            echo $row["comment"]." ";
            echo $row["date"]."<br>";
            echo "<hr>";
        }
        ?>

    </body>
</html>

