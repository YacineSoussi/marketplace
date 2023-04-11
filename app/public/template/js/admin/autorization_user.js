//  Requête Ajax qui permet de verouillé/déverouillé un utilisateur 
function onClickBtnOpen(event) {
    event.preventDefault();

    const icone = this.querySelector('i'); 
    const url = this.href; 
  
    axios.get(url).then(function(response) {
      
      if(icone.classList.contains('fa-check')) {
      icone.classList.replace('fa-check', 'fa-times');
      
     } 
     else if (icone.classList.contains('fa-times')) {
       icone.classList.replace('fa-times', 'fa-check');
        
      } 
    }
    )
  }

  document.querySelectorAll('a.js-open').forEach(function(link) {
     /* On récupère tous les liens qui ont la classe js-open & on va déclencher une fonction au click de ce lient */
   link.addEventListener('click', onClickBtnOpen) 
   /* $(link).on('click', onClickBtnOpen) */
   
  })
  