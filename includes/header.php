<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome Food City</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/welcome food city/assets/css/custom.css" rel="stylesheet">
   
  <style>
    body {
      padding-top: 80px; /* Prevent content from hiding behind navbar */
      background-color: #f8f9fa;
    }

    .navbar input[type="search"] {
      width: 425px;
      
    }

    .card:hover {
      transform: translateY(-5px);
      transition: all 0.3s ease;
    }

    .hover-shadow:hover {
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    #suggestion-box {
     max-height: 250px;
     overflow-y: auto;
     cursor: pointer;
    }
  </style>
</head>
<body>

<!-- ✅ Fixed Navbar with Search -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
 <span class="badge bg-warning text-dark" id="cart-count"><?= count($_SESSION['cart'] ?? []) ?></span>
 
<div class="container">
    <a class="navbar-brand" href="/welcome_food_city/index.php">Welcome Food City</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <!-- Left Links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/welcome_food_city/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/welcome_food_city/pages/cart.php">Cart</a></li>
        <li class="nav-item"><a class="nav-link" href="/welcome_food_city/pages/login.php">Login</a></li>
      </ul>

      <!-- Search Form with Suggestions -->
<form class="position-relative" autocomplete="off">
  <input 
    id="live-search" 
    class="form-control me-2" 
    type="search" 
    name="q" 
    placeholder="Search for products..." 
    aria-label="Search"
  >
  <!-- Dropdown for suggestions -->
  <ul id="suggestion-box" class="list-group position-absolute w-100 z-3" style="top:100%; display:none;"></ul>
 
 <script>
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.getElementById("live-search");
  const suggestionBox = document.getElementById("suggestion-box");

  searchInput.addEventListener("keyup", function () {
    const query = this.value.trim();
    if (query.length > 1) {
      fetch("/welcome_food_city/ajax/search_suggestions.php?term=" + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
          suggestionBox.innerHTML = "";
          if (data.length > 0) {
            data.forEach(item => {
              const li = document.createElement("li");
              li.classList.add("list-group-item", "list-group-item-action");
              li.textContent = item;
              li.addEventListener("click", () => {
                window.location.href = `/welcome_food_city/pages/search.php?q=${encodeURIComponent(item)}`;
              });
              suggestionBox.appendChild(li);
            });
            suggestionBox.style.display = "block";
          } else {
            suggestionBox.style.display = "none";
          }
        });
    } else {
      suggestionBox.style.display = "none";
    }
  });

  document.addEventListener("click", function (e) {
    if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
      suggestionBox.style.display = "none";
    }
  });
});
</script>

</form>


    </div>
  </div>
</nav>
