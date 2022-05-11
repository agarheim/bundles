"use strict";
// Ваш скрипт

let countOfFields = 0; // Текущее число полей
let curFieldNameId = 1; // Уникальное значение для атрибута name
let maxFieldLimit = 8; // Максимальное число возможных полей
let index_shops;


function deleteField(a) {
// Получаем доступ к ДИВу, содержащему поле
let contDiv = a.parentNode;
// Удаляем этот ДИВ из DOM-дерева
contDiv.parentNode.removeChild(contDiv);
// Уменьшаем значение текущего числа полей
countOfFields--;
// Возвращаем false, чтобы не было перехода по сслыке
return false;
}


function addField() {
const link = document.querySelectorAll("[data-index]");

link.forEach(el=>{
   index_shops = el.getAttribute('data-index');
});
countOfFields = index_shops;
// Проверяем, не достигло ли число полей максимума

// if (countOfFields >= maxFieldLimit) {
// alert("Число полей достигло своего максимума = " + maxFieldLimit);
// return false;
// }
// Увеличиваем текущее значение числа полей
countOfFields++;
// Увеличиваем ID
curFieldNameId++;
// Создаем элемент ДИВ
let div = document.createElement("div");
//div.onclick = function(e) { this.parentNode.removeChild(this) };
div.className = "input-group mb-3";
// Добавляем HTML-контент с пом. свойства innerHTML
div.innerHTML = '<div class="input-group-text"><input type="checkbox" class="mt-0" name="shop_name['+countOfFields+'][active]" id="shop-code-1" value="active"></div>'+
                   '<input name="shop_name['+countOfFields+'][name]" class="form-control" type="text" /> '+
                   '<input type="text" class="form-control" name="shop_name['+countOfFields+'][code]" id="shop-code-1" data-index="'+countOfFields+'"  value="" required>'+
                   '<a onclick="return deleteField(this)" class="input-group-text"  href="#"><i class="fa fa-times"></i></a>';
// Добавляем новый узел в конец списка полей
document.getElementById("shops").appendChild(div);
// Возвращаем false, чтобы не было перехода по сслыке
return false;
}
