jQuery(document).ready(function (){
    // ustawienie pół po stronie widoku
    setFields();
    
    jQuery('#init-btn').click(function (){
        //pobranie pól i zapisanie do tablicy
        var paramsArr = getParams();
        findWay(paramsArr);
    });
})

function setFields() {
    jQuery('.cell').click(function (){
        if(jQuery(this).attr('data-cell-type') == 0){
            //Step 1 (wybór startu)
            if(jQuery(this).closest('.board').hasClass('board1')) {
                jQuery(this).attr('id','start-cell');
                jQuery(this).attr('data-cell-type', 1);
                jQuery(this).closest('.board').removeClass('board1');
                jQuery(this).closest('.board').addClass('board2');
            }
            //Step 2 (wybór końca)
            else if(jQuery(this).closest('.board').hasClass('board2')) {
                jQuery(this).attr('id','last-cell');
                jQuery(this).attr('data-cell-type', 2);
                jQuery(this).closest('.board').removeClass('board2');
                jQuery(this).closest('.board').addClass('board3');
            }
            //Step 3 (wybór przeszkód)
            else {
                jQuery(this).addClass('obstacle');
                jQuery(this).attr('data-cell-type', 3);
            }
        }
    });
}

function getParams() {
    // emptyCell = 0; firstCell = 1; lastCell = 2; obstacleCell = 3;
    var cellsArr = [];
    
    var i;
    var j;
    // budowanie tablicy argumentów
    for(i = 1; i < 21; i++){
        cellsArr[i] = [];
        for(j = 1; j < 21; j++){
            cellsArr[i][j] = jQuery("[data-cell-id='" + i + "-" + j + "']").attr('data-cell-type');
        }
    }
    return cellsArr;
}


function findWay(paramsArr) {
    var homeURL = window.location.href;
    jQuery.ajax({
       url: homeURL + 'engine.php', 
       type:'GET',
       data: { 
           param1: paramsArr,
       },
       success: function(response){
           var resp = JSON.parse(response);
           var respType = resp.length;
               if(respType > 1) {
                   for (var x in resp) {
                       var cellAtrr = resp[x];
                       console.log(cellAtrr);
                       jQuery("[data-cell-id='" + cellAtrr + "']").addClass('blue');
                   }
                   jQuery('.board').removeClass('board3');
                   jQuery('.board').addClass('board4');
                   /*setTimeout(function(){
                    jQuery('.board.board4 .board-finish').show();
                       var finishVideo = '<iframe width="600" height="400" src="https://www.youtube.com/embed/a-BgREkkjcg?autoplay=1" frameborder="0" autoplay="true" allow="autoplay; encrypted-media" allowfullscreen></iframe>';
                       jQuery('#box-video').html(finishVideo);
                       setTimeout(function(){
                           jQuery('.board.board4 .board-finish').hide();
                       }, 7000);
                   }, 1000); */
               }
                if(respType == 1) {
                    jQuery('.board').removeClass('board3');
                    jQuery('.board').addClass('board4');
                    jQuery('.board.board4 .board-finish').show();
                    var finishText = '<h2 class="message-wrong">Nie można znaleźć ścieżki!</h2';
                    jQuery('#box-video').html(finishText);
                }
                
           }
           
           
    }); 
}
