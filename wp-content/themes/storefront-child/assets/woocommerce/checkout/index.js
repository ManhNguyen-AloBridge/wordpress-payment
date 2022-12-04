(function ($) {
  $("select").on("change", function (e) {
    $.ajax({
        method: "get",
        url: ipAjaxVar.ajaxurl,
        data: {
          action: "prefix_update_existing_cart_item_meta",
          'quantity':$(this).val()
        },
      })
      .done(function (data) {
        console.log(data);
        location.reload(true);
      });
    e.preventDefault();
  });
})(jQuery);
