let div = document.createElement('div');

const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const name_prod = urlParams.get('name');

let content;
content = '<div class="wrapper">';

  content += '<p>';
    content += 'Showroom Priximbattable';
  content += '</p>';
  content +='<p>'+ name_prod +'</p>';

  content += '<button onclick="window.close()">';
    content += 'Fermer et retourner au site';
  content += '</button>';

content += '</div>';

div.innerHTML = content;

document.body.appendChild(div);
