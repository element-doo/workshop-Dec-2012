module Store
{
    root Product
    {
        string Name;
        money Price;
        Group? *Group;
        timestamp ModifiedAt { versioning; }
    }
    
    snowflake ProductList Product
    {
        Name;
        Price;
        order by Price desc, Name asc;
    }

    root Group
    {
        string Name;
        detail Products Product.Group;
    }
}
