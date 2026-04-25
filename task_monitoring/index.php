<?php
// --- DATABASE CONNECTION ---
$host = "localhost"; $user = "root"; $pass = ""; $db = "task_manager";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- FORM LOGIC ---
if (isset($_POST['add_task'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $conn->query("INSERT INTO tasks (title, deadline, priority) VALUES ('$title', '$deadline', '$priority')");
    header("Location: " . $_SERVER['PHP_SELF']); // Refresh to prevent form resubmission
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM tasks WHERE id=$id");
    header("Location: index.php");
}

if (isset($_GET['complete'])) {
    $id = (int)$_GET['complete'];
    $conn->query("UPDATE tasks SET status='Completed' WHERE id=$id");
    header("Location: index.php");
}

// --- FETCH DATA ---
$tasks = $conn->query("SELECT * FROM tasks ORDER BY status DESC, deadline ASC");
$stats = $conn->query("SELECT COUNT(*) as total, SUM(status='Completed') as completed, SUM(status='Pending') as pending FROM tasks")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>TaskMaster Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .glass-sidebar { background: linear-gradient(180deg, #1e3c72 0%, #2a5298 100%); }
        /* Custom scrollbar for a cleaner look */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-50 flex flex-col md:flex-row min-h-screen">

    <div class="md:hidden glass-sidebar p-4 text-white flex justify-between items-center shadow-lg">
        <h1 class="text-xl font-bold">Task Monitoring.</h1>
        <div class="text-xs bg-white/20 px-3 py-1 rounded-full">
            Done: <?= $stats['completed'] ?? 0 ?>
        </div>
    </div>

    <aside class="hidden md:flex w-72 glass-sidebar text-white p-8 flex-col shadow-2xl">
        <h1 class="text-3xl font-extrabold mb-12 tracking-tight">Task Monitoring</h1>
        <nav class="space-y-6 flex-1">
            <a href="#" class="flex items-center space-x-4 p-3 bg-white/10 rounded-2xl shadow-inner">
                <span>📊</span> <span class="font-semibold">Dashboard</span>
            </a>
        </nav>
        <div class="mt-auto p-4 bg-white/5 rounded-2xl text-sm border border-white/10">
            <p class="opacity-70">Logged in as</p>
            <p class="font-bold">Admin User</p>
        </div>
    </aside>

    <main class="flex-1 p-4 md:p-12 lg:p-16 max-w-7xl mx-auto w-full">
        
        <header class="mb-10">
            <h2 class="text-2xl md:text-4xl font-bold text-slate-800">Your Tasks</h2>
            <p class="text-slate-500 mt-1">Organize your day, one task at a time.</p>
        </header>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total</p>
                <p class="text-2xl font-black text-slate-800"><?= $stats['total'] ?? 0 ?></p>
            </div>
            <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Completed</p>
                <p class="text-2xl font-black text-teal-500"><?= $stats['completed'] ?? 0 ?></p>
            </div>
            <div class="bg-blue-600 p-5 rounded-3xl shadow-lg shadow-blue-200 hidden md:block">
                <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Efficiency</p>
                <p class="text-2xl font-black text-white">
                    <?= $stats['total'] > 0 ? round(($stats['completed']/$stats['total'])*100) : 0 ?>%
                </p>
            </div>
        </div>

        <section class="bg-white p-6 md:p-8 rounded-3xl shadow-xl border border-slate-100 mb-10">
            <form method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-5">
                    <label class="text-sm font-bold text-slate-700 mb-2 block">Task Title</label>
                    <input type="text" name="title" required placeholder="What needs to be done?" 
                           class="w-full p-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <div class="md:col-span-3">
                    <label class="text-sm font-bold text-slate-700 mb-2 block">Due Date</label>
                    <input type="datetime-local" name="deadline" required 
                           class="w-full p-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-bold text-slate-700 mb-2 block">Priority</label>
                    <select name="priority" class="w-full p-4 rounded-2xl bg-slate-50 border-none focus:ring-2 focus:ring-blue-500 outline-none transition appearance-none">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Med</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" name="add_task" 
                            class="w-full bg-blue-600 text-white p-4 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-100 active:scale-95">
                        Add
                    </button>
                </div>
            </form>
        </section>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if($tasks->num_rows > 0): ?>
                <?php while($row = $tasks->fetch_assoc()): ?>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition group">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                                <?= $row['priority'] == 'High' ? 'bg-red-100 text-red-600' : ($row['priority'] == 'Medium' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600') ?>">
                                <?= $row['priority'] ?>
                            </span>
                            <span class="text-xs font-bold text-slate-400">#<?= $row['id'] ?></span>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800 mb-1 leading-tight <?= $row['status'] == 'Completed' ? 'line-through opacity-40' : '' ?>">
                            <?= htmlspecialchars($row['title']) ?>
                        </h4>
                        <p class="text-sm text-slate-400 mb-6">
                            📅 <?= date('M d, h:i A', strtotime($row['deadline'])) ?>
                        </p>
                    </div>
                    
                    <div class="flex gap-3 mt-auto pt-4 border-t border-slate-50">
                        <?php if($row['status'] != 'Completed'): ?>
                            <a href="?complete=<?= $row['id'] ?>" class="flex-1 text-center py-3 bg-teal-50 text-teal-600 font-bold rounded-xl hover:bg-teal-500 hover:text-white transition active:scale-95">
                                Complete
                            </a>
                        <?php endif; ?>
                        <a href="?delete=<?= $row['id'] ?>" class="px-4 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-500 hover:text-white transition active:scale-95" onclick="return confirm('Remove task?')">
                            ✕
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20">
                    <p class="text-slate-400 font-medium">No tasks found. Start by adding one above! 🚀</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 flex justify-around p-3 z-50">
        <button class="text-blue-600 text-xl">🏠</button>
        <button class="text-slate-400 text-xl">🔍</button>
        <button class="text-slate-400 text-xl">⚙️</button>
    </div>
    
    <div class="h-16 md:hidden"></div>

</body>
</html>