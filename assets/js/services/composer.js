export default {

    'authCmd': function (username, token) {
        let host = window.location.hostname;
        return `composer config --global --auth http-basic.${host} ${username} ${token}`;
    },
    'repositoriesConfig': function () {
        let host = window.location.protocol+'//'+ window.location.hostname+(window.location.port ? ':'+window.location.port: '');
        return `{
    "repositories": [
        {"type": "composer", "url": "${host}"},
        {"packagist.org": false}
    ]
}`;
    },

}