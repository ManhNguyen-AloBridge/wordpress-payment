(function ($) {
  function active() {
    console.log(666);
    jQuery
      $.ajax({
        method: "get",
        url: ipAjaxVar.ajaxurl,
        data: {
          action: "prefix_update_existing_cart_item_meta",
        },
      })
      .done(function (msg) {
        // Do something when done
      });
  }

  active();
});
