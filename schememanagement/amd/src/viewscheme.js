define(['jquery', 'core/ajax'], function($, ajax) {
	return {
        init: function() {
            $(".inforationschema").on('click', function(e) {
                $("#gender").empty();
                $("#category").empty();
                var genderlist = JSON.parse($(e.currentTarget).attr("data-gender"));
                genderlist.forEach(function(gender) {
                  $("#gender").append("<li>" + gender + "</li>");
                });
                var categorylist = JSON.parse($(e.currentTarget).attr("data-category"));
                categorylist.forEach(function(category) {
                  $("#category").append("<li>" + category + "</li>");
                });
                $("#title").html($(e.currentTarget).attr("data-title"));
                $("#subscheme").html($(e.currentTarget).attr("data-subscheme"));
                $("#batchsize").html($(e.currentTarget).attr("data-batchsize"));
                $("#desc").html($(e.currentTarget).attr("data-desc"));
                $("#ownername").html($(e.currentTarget).attr("data-schemeowner"));
                $("#funds").html($(e.currentTarget).attr("data-funds"));
                });
            }
        };
    });