<?

shell_exec("python3 twitter.py");

$json_data = json_decode(file_get_contents('twitter.json'));

echo "<div style='display: flex; flex-wrap: wrap; justify-content: center; align-items: center;'>";
foreach($json_data as $data) {
?>
    <div style="margin: 10px; text-align: center; width:100%">
        <a href="<? echo $data->video ?>">
            <img style="max-width:80%" src="<? echo $data->img ?>">
        </a>
    </div>
<?
}
echo "</div>";
?>