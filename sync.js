function smallSync(username, callback) {
    $.get("smallSync.php?username=" + username, null, function (data) {

        callback(username, data)

    });
}

function getMassPosts(username, callback) {
    $.get("getAllPostsToDB.php?username=" + username, null, function (data) {

        callback(username, data);

    });
}

function smallBack(username, data) {

    if (data == "{'doreload': true}") {

        console.log("Syncing With The Steem Blockchain");

        setTimeout(function () {
            smallSync(username, smallBack);
        }, 1000);

    } else {
        toBeta();
    }
}

function massBack(username, data) {

    if (data == "{'doreload': true}") {
        console.log("Grabbing Posts");

        setTimeout(function () {
            getMassPosts(username, massBack);
        }, 1000);

    } else {
        toBeta();
    }
}

function sync(username) {
    if (username != "") {
        $.get("checkSync.php?username=" + username, null, function (data) {

            if (data == "indexed") {

                smallSync(username, smallBack);

            } else if (data == "unindexed") {

                getMassPosts(username, massBack);

            }

        });
    }
}


/*function sync(username) {
    if (username != "") {
        $.get("checkSync.php?username=" + username, null, function (data) {

            if (data == "indexed") {

                var odata = "{'doreload': true}";

                while (odata == "{'doreload': true}") {

                    $.get("smallSync.php?username=" + username, null, function (data) {

                        console.log("oogaboogaindexed");

                        odata = data;

                        console.log(data);

                    });

                }

            } else if (data == "unindexed") {

                var odata = "{'doreload': true}";

                while (odata == "{'doreload': true}") {

                    $.get("getAllPostsToDB.php?username=" + username, null, function (data) {
                        console.log("oogaboogaunindexed");
                        odata = data;
                        console.log(data);
                    });

                }

            }

        });
    }
}

*/