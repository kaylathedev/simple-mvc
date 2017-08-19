<a href="/posts/view/<?php echo $item['id']; ?>">Go Back</a>
<h2>Editing Post</h2>
<form method="POST">
<table>
    <tbody>
        <tr>
            <td>Title</td>
            <td><input type="text" name="Post[title]" placeholder="<?php echo h($item['title']);?>"></td>
        </tr>
        <tr>
            <td>Content</td>
            <td><input type="text" name="Post[content]" placeholder="<?php echo h($item['content']);?>"></td>
        </tr>
        <tr style="text-align:center">
            <td><input type="reset" value="Reset"></td>
            <td><input type="submit" value="Update"></td>
        </tr>
    </tbody>
</table>
</form>
