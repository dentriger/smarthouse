$(document).ready(function () {
    $("#column_2").hide();
});
$("#phones").hover(function (e) {
    //This funciton defines what happens on mouse-in or hover.

    console.log('hover');
    $().alert('ddddd');
    $("#column_3").hide();
    $("#column_2").show();


}, function (e) {
    //This function defines what happens on mouse-out or when the hover is over.
    $("#column_3").show();
    $("#column_2").hide();

});
var owl = $(".owl-carousel");
owl.owlCarousel({
    loop:true,
    margin:10,
    nav:true,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:3
        },
        1000:{
            items:5
        }
    }
})