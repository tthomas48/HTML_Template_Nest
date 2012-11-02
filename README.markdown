HTML_Template_Nest
==================
Well-Formed xml templating for PHP
----------------------------------

HTML_Template_Nest provides xml based templating based on JSP 2.0. Documents are well formed and can be trivially added to with custom tags. Below is a trivial grocery list.

    <html xmlns:c="urn:nsttl:HTML_Template_Nest_Taglib_Standard">
      <body>
        <h3>Shopping List</h3>
        <ul>
          <c:foreach items="groceryList" var="shoppingListItem">
            <li>${shoppingListItem}</li>
          </c:foreach>
        </ul>
      </body>
    </html>

See (http://tthomas48.github.com/HTML_Template_Nest/) for full documentation. 
