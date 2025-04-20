<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="{{set_active(['admin'])}}">
                    <a href="{{ route('admin.home') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                {{-- <li class="{{set_active(['admin/blogs', 'admin/blogs/create'])}}">
                    <a href="{{ route('blogs') }}">
                        <i class="fas fa-newspaper"></i>
                        <span>Blogs</span>
                    </a>
                </li>
                <li class="{{set_active(['admin/categories','admin/categories/create'])}}">
                    <a href="{{ route('category') }}">
                        <i class="fas fa-th-large"></i>
                        <span>Category</span>
                    </a>
                </li> --}}
                <li class="{{set_active(['admin/menu','admin/menu/create'])}}">
                    <a href="{{ route('menu') }}">
                        <i class="fas fa-envelope"></i>
                        <span>Menu</span>
                    </a>
                </li>
                <li class="{{set_active(['admin/menu/category','admin/menu/category/create'])}}">
                    <a href="{{ route('menu-category') }}">
                        <i class="fas fa-envelope"></i>
                        <span>Menu Category</span>
                    </a>
                </li>
                <li class="{{set_active(['admin/comments'])}}">
                    <a href="{{ route('comments') }}">
                        <i class="fas fa-envelope"></i>
                        <span>Comments</span>
                    </a>
                </li>
                <li class="{{set_active(['admin/pages','admin/pages/create'])}}">
                    <a href="{{ route('pages') }}">
                        <i class="fas fa-file-alt"></i>
                        <span>Pages</span>
                    </a>
                </li>
                <li class="{{set_active(['admin/testimonials','admin/testimonials/create'])}}">
                    <a href="{{ route('testimonials') }}">
                        <i class="fas fa-star"></i>
                        <span>Testimonials</span>
                    </a>
                </li>
                <li class="{{set_active(['admin/collection','admin/collection/create'])}}">
                    <a href="{{ route('collection') }}">
                        <i class="fab fa-envira"></i>
                        <span>Album</span>
                    </a>
                </li>
                <li class="{{set_active(['admin/ccat','admin/ccat/create'])}}">
                    <a href="{{ route('ccat') }}">
                        <i class="fab fa-envira"></i>
                        <span>Album Category</span>
                    </a>
                </li>
                <li class="{{set_active(['admin/setting/page','admin/setting/contact'])}}">
                    <a href="{{ route('setting/page') }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

