<h2>Help</h2>
<p>This website uses a very simple and fast framework, made by Michael.
It follows the MVC pattern, which is very easy to build applications upon.</p>
<p>If you are familiar with the frameworks, CakePHP or CodeIgniter, this is built using the ideas of them two.</p>
<p><a href="http://www.bennadel.com/blog/2379-a-better-understanding-of-mvc-model-view-controller-thanks-to-steven-neiland.htm">What is MVC?</a> Please briefly read that link before you continue.</p>
<h2>I can't view posts, help!</h2>
<p>Go into your MySQL by using any software you want.</p>
<p>Then, create a database, "example". In that database, make a table, "posts", with two text columns. The two columns are named "title" and "content".</p>
<h2>Cool, now what is all of this stuff?</h2>
<p>In the website's root directory, you'll see two folders, and 2 files.</p>
<table border="2" cellpadding="7">
<tr>
<td><code>app\</code></td>
<td>Holds files related to the application (or website).</td>
</tr>
<tr>
<td><code>lib\</code></td>
<td>Holds classes that help run the application.<br>Also has commonly used classes, such as database access, and logging.</td>
</tr>
<tr>
<td><code>.htaccess</code></td>
<td>An Apache configuration file.<br>This is useul for sending all requests to <code>index.php</code>.</td>
</tr>
<tr>
<td><code>index.php</code></td>
<td>The "door" of the application.<br>This takes a request, makes the controller object, and then displays the view.</td>
</tr>
</table>
<p>You should never mess with the <code>lib</code> folder.</p>
<h2>What Happens Behind Every Page</h2>
<p>
A url usually looks like this. <code>http://example.com/<strong>controllername</strong>/<strong>action</strong>/<strong>param1</strong>/<strong>param2</strong></code>
<p>When a user requests a page, the code in <code>index.php</code> initializes everything.
Then the controller class, called <code><strong>controllername</strong>Controller</code> is made, and the function <code><strong>action</strong></code> is called.</p>
<p>After the function is done, then the appropriate view is displayed (or echoed).
In the previous example, the file <code>app\view\animals\add.php</code> is displayed.</p>
<div style="margin:0 0 0 3em">
<div style="font-size:1.1em"><strong>Example</strong></div>
<p>Let's pretend that the user goes to <code>http://example.com/animals/add</code>.<br>
The class <code>AnimalsController</code> is made, and the function, <code>add()</code> inside the class is ran.</p>
<p>Or if the user might go to <code>http://example.com/clothing/edit/size</code>.<br>
The class <code>ClothingController</code> is made, and the function, <code>edit("size")</code> inside the class is ran.</p>
</p>
</div>
<h2>OK, but what are all of the folders in <code>app/</code>?</h2>
<ul>
    <li><code>conifg</code>
        <p>Your configuration files are here. These are automatically loaded.</p>
    </li>
    <li><code>controllers</code>
        <p>These are where the controllers are usually kept.</p>
    </li>
    <li><code>models</code>
        <p>These are where the models are usually kept. (I'll explain more about models later.)</p>
    </li>
    <li><code>public</code>
        <p>This is anything that should be accessed without controllers.<br>For example, images, CSS stylesheets, JavaScripts, etc.
    </li>
    <li><code>tmp</code>
        <p>Reserved for the framework. Usually, log files are placed here.</p>
    </li>
    <li><code>views</code>
        <p>Has folders of anything that will be displayed.<br>Files are usually called "<code>controllername\action.php</code>".</p>
    </li>
</ul>
<h2>What now?</h2>
<p>Take a look at each file in the app folder, especially the "<code>PostsController</code>" class.</p>