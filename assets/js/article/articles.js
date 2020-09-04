import {Routing} from "../routing";
import {toast} from "../alert";

$(document).ready(() => {
    let $jsArticle = $(".js-articles");
    $("#more-articles").click(() => {
        let lastArticleId = $jsArticle.data("last-article-id");
        $.get(Routing.generate("app_articles_cards", {lastId: lastArticleId}), response => {
            $("#articles-list").append(response["cards"]);
            $jsArticle.data("last-article-id", response["last_article_id"]);
        }).fail(err => {
           toast(err.responseJSON);
        });
    });
});