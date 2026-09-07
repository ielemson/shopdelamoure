<div class="align-self-center ec-header-search">
    <div class="header-search">
        <form class="ec-search-group-form" action="{{ url('/shop') }}" method="GET">
            <div class="ec-search-select-inner">
                <select name="category">
                    <option selected disabled>Category</option>
                    <option value="home-kitchen">Home & Kitchen</option>
                    <option value="electronics">Electronics</option>
                    <option value="fashion">Fashion</option>
                    <option value="office-furniture">Office Furniture</option>
                    <option value="hotel-furniture">Hotel Furniture</option>
                </select>
            </div>

            <input class="form-control" name="search" placeholder="Search products..." type="text">

            <button class="search_submit" type="submit">
                <i class="fi-rr-search"></i>
            </button>
        </form>
    </div>
</div>