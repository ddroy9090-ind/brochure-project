<div class="top-navbar">
  <h2><span>HouzzHunt</span></h2>
  <div class="right-nav">
    <div class="search-bar">
      <input type="text" placeholder="Search...">
      <span><img src="assets/icons/search.png" alt="" width="14"></span>
    </div>

    <div class="user-menu" id="userMenu">
      <img src="assets/icons/profile.png" alt="User">
      <h6><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></h6>
      <ul class="dropdown-menu" id="dropdownMenu">
        <li><a href="#">Profile</a></li>
        <li><a href="#">Settings</a></li>
        <!-- Point to logout.php (NOT login.php) -->
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</div>
