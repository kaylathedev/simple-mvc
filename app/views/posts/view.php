<a href="/posts">Go Back</a>
<h2>Post</h2>
<div style="float:right">
<form method="POST" action="/posts/delete/<?php echo $item['id'];?>">
<input type="submit" value="Delete">
</form>
</div>
<table class="alternating-table">
    <tbody>
        <tr>
            <td>Title</td>
            <td><?php echo h($item['title']);?></td>
        </tr>
        <tr>
            <td>Content</td>
            <td><?php echo h($item['content']);?></td>
        </tr>
    </tbody>
</table>
<a href="/posts/edit/<?php echo $item['id'];?>">Edit</a>
