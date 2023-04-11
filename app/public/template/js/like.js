$(document).ready(function(){


    function onClickBtnLike(event){
        event.preventDefault();

        const url = this.href;
        const spanCount = this.querySelector('span.js-likes');
        const icon = this.querySelector('i');
        const spanNavCount = document.querySelector('span.js-likes-nav');

        //fait appel à la requete http du like ou du dislike
        axios.get(url).then(function(response) {

            //remplace par le nouveau nombre de likes pour le produit
            spanCount.textContent = response.data.likes;

            //remplace par le nouveau nombre de likes de l'utilisateur
            spanNavCount.textContent = response.data.likes_user;


            //changer l'icone selon le like ou le dislike
            if(icon.classList.contains('fas')) icon.classList.replace('fas', 'far')
            else icon.classList.replace('far', 'fas')

        //gérer l'erreur
        }).catch(function(error) {
            if(error.response.status === 403){
                window.alert("Vous ne pouvez pas liker un produit si vous n'êtes pas connecté !")
            }else{
                window.alert("Erreur, veuillez réessayer plus tard !")
            }
        })
    }


    function onClickBtnLikeDelete(event){
        event.preventDefault();

        const url = this.href;
        const spanNavCount = document.querySelector('span.js-likes-nav');


        const product = document.querySelector("div."+this.id);

        //fait appel à la requete http du like ou du dislike
        axios.get(url).then(function(response) {


            //remplace par le nouveau nombre de likes de l'utilisateur
            spanNavCount.textContent = response.data.likes_user;

            //supprimer le produit
            product.remove();


        //gérer l'erreur
        }).catch(function(error) {
 
        })
    }



    document.querySelectorAll('a.js-like').forEach(function(link){
      link.addEventListener('click', onClickBtnLike);
    })

    document.querySelectorAll('a.js-like-delete').forEach(function(link){
      link.addEventListener('click', onClickBtnLikeDelete);
    })

})