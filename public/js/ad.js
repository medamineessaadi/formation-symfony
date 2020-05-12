$('#add-image').click(function(){

    //je recupere le numero des futures champs que je veux creer  
    const index =+$('#widgets_counter').val();
 
    // le prototype des entres 
    const tmpl=$('#ad_images').data('prototype').replace(/__name__/g, index);
    
    // j'injecte le code au sein de la div 
    $('#ad_images').append(tmpl);
    $('#widgets_counter').val(index + 1);
    // je gere le boutton supprimer
    handleDeleteButtons();
});
function handleDeleteButtons()
{
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    })
}
function updateCounter()
{
    const count = +$('#ad_images div.form-group').length;
    $('#widgets_counter').val(count);
}
updateCounter();
handleDeleteButtons();