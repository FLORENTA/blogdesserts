import '../../css/create-article.scss';
import 'selectize';

let $imageCollectionHolder, $categoryCollectionHolder;
let $addImage = $('<button class="add_image btn btn-dark mt-2"><i class="fas fa-plus-square"></i> Nouvelle image</button>');
let $addCategory = $('<button class="add_category btn btn-dark mt-2"><i class="fas fa-plus-square"></i> Nouvelle catégorie</button>');
let $newImageLinkLi = $('<li></li>').append($addImage);
let $newCategoryLinkLi = $('<li></li>').append($addCategory);

function addFormDeleteLink($tagFormLi) {
    let $removeFormButton = $('<button class="btn btn-danger mt-2"><i class="fa fa-trash"></i> Supprimer</button>');
    $tagFormLi.append($removeFormButton);
    $removeFormButton.on('click', function(e) {
        $tagFormLi.remove();
    });
}

function addForm($collectionHolder, $newLinkLi) {
    let prototype = $collectionHolder.data('prototype');
    let index = $collectionHolder.data('index');
    let newForm = prototype;
    newForm = newForm.replace(/__name__/g, index);
    $collectionHolder.data('index', index + 1);
    let $newFormLi = $('<li></li>').append(newForm);
    $newLinkLi.before($newFormLi);
    addFormDeleteLink($newFormLi);
}

$(document).ready(function() {
    $imageCollectionHolder = $("#article_images");
    // $categoryCollectionHolder = $("#article_categories");
    $imageCollectionHolder.children().each(function() {
        addFormDeleteLink($(this));
    });
    // $categoryCollectionHolder.children().each(function() {
    //     addFormDeleteLink($(this));
    // });
    $imageCollectionHolder.append($newImageLinkLi);
    // $categoryCollectionHolder.append($newCategoryLinkLi);
    $imageCollectionHolder.data('index', $imageCollectionHolder.find('input').length);
    // $categoryCollectionHolder.data('index', $categoryCollectionHolder.find('input').length);
    $addImage.on('click', function(e) {
        e.preventDefault();
        addForm($imageCollectionHolder, $newImageLinkLi);
    });
    // $addCategory.on('click', function(e) {
    //     e.preventDefault();
    //     addForm($categoryCollectionHolder, $newCategoryLinkLi);
    // });

    $("select").selectize();
});