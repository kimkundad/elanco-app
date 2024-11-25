<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Poppins:ital,wght@0,500;1,500&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" media="all" href="{{ url('admin/css/app.min.css') }}">

    <script>
      var viewportmeta = document.querySelector('meta[name="viewport"]');
      if (viewportmeta) {
        if (screen.width < 375) {
          var newScale = screen.width / 375;
          viewportmeta.content = 'width=375, minimum-scale=' + newScale + ', maximum-scale=1.0, user-scalable=no, initial-scale=' + newScale + '';
        } else {
          viewportmeta.content = 'width=device-width, maximum-scale=1.0, initial-scale=1.0';
        }
      }
    </script>

    <style>

            .dropdown {
}

/* Toggle Button */
.dropdown-toggle {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
}

.icon-dots {
    width: 24px;
    height: 24px;
    fill: #666;
}

/* Dropdown Menu */
.dropdown-menu {
    position: absolute;
    top: 30px; /* Adjust based on your button size */
    right: 0;
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 10px 0;
    display: none; /* Initially hidden */
    z-index: 100;
   width: 200px
}

/* Menu Items */
.dropdown-item {
    display: flex;
    align-items: center;
    padding: 8px 24px;
    text-decoration: none;
    color: #808191;
    font-size: 14px;
    transition: background 0.2s ease;
}

.dropdown-item:hover {
    background: #f5f5f5;
}

.dropdown-item .icon {
    width: 16px;
    height: 16px;
    margin-right: 8px;
}

/* Delete Item Style */
.dropdown-item.delete {
    color: #e53935;
}

/* Show Dropdown */
.dropdown:hover .dropdown-menu {
    display: block;
}
.eye_icon{
    width: 24px;
    height: 24px;
    margin-right: 5px
}
.actions__btn .icon {
    font-size: 12px;
    fill: #808191;
    opacity: 0.4;
    transition: all 0.25s;
}
.Flag_icon{
    height:28px;
    border-radius: 25px;
}
.user_icon{
    height:32px;
    border-radius: 25px;
}
.slider__download {
    width: 45px;
    height: 45px;
    background: #ffffff;
    border: none;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}
.sidebar__item.active {
    background: #2b71b6;
    color: #ffffff;
}
.widget__item:before {
    background: #2b71b6;
}
.btn_black:hover {
    background: #2b71b6;
}
</style>
