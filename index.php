function openToDoOnClick(checklistId, checklistName, checklistDescription) {
    loadToDoExpanded(checklistId, checklistName, checklistDescription);
}

function loadToDoExpanded(checklistId, checklistName, checklistDescription) {
    var jsonData = {
        "action": "LOADITEMS",
        "checklistId": checklistId
    };

    $.ajax({
        url: "data/applicationLayer.php",
        type: "POST",
        data: jsonData,
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        success: function (jsonResponse) {
            loadToDoDataExpanded(jsonResponse, checklistName, checklistDescription);
        },
        error: function (errorMessage) {
            alert(errorMessage.responseText);
        }
    });
}

function loadToDoDataExpanded(itemsToLoad, checklistName, checklistDescription) {
    var htmlTag = $("#toDoExpandedList");
    htmlTag.append('<div><h2 style="text-align: center;">' + checklistName + '</h2>');
    htmlTag.append('<p style="text-align: center;">' + checklistDescription + '</p></div>');

    $.each(itemsToLoad, function (key, value) {
        var cardHtml = '<li class="mdl-list__item itemDetail"><i class="material-icons" style="padding-right: 10px;color:#4054B2">radio_button_checked</i><span><span class="mdl-list__item-primary-content itemSpan"><h6>' + value["itemName"] + '</h6></span><p class="expandedNotes">' + value["notes"] + '</p></li></span>'
        htmlTag.append(cardHtml);
    });

    $("#toDoChecklists").hide();
    $("#addToDo").hide();
    $("#toDoExpanded").show();
}

function openToBringOnClick(checklistId, checklistName, checklistDescription) {
    loadToBringExpanded(checklistId, checklistName, checklistDescription);
}

function loadToBringExpanded(checklistId, checklistName, checklistDescription) {
    var jsonData = {
        "action": "LOADITEMS",
        "checklistId": checklistId
    };

    $.ajax({
        url: "data/applicationLayer.php",
        type: "POST",
        data: jsonData,
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        success: function (jsonResponse) {
            loadToBringDataExpanded(jsonResponse, checklistName, checklistDescription);
        },
        error: function (errorMessage) {
            alert(errorMessage.responseText);
        }
    });
}

function loadToBringDataExpanded(itemsToLoad, checklistName, checklistDescription) {
    var htmlTag = $("#toBringExpandedList");
    htmlTag.append('<h2 style="text-align: center;">' + checklistName + '</h2>');
    htmlTag.append('<p style="text-align: center;">' + checklistDescription + '</p>');

    $.each(itemsToLoad, function (key, value) {
        var cardHtml = '<li class="mdl-list__item itemDetail"><i class="material-icons" style="padding-right: 10px;color:#4054B2">radio_button_checked</i><span><span class="mdl-list__item-primary-content itemSpan"><h6>' + value["itemName"] + '</h6></span><p class="expandedNotes">' + value["notes"] + '</p></li></span>'
        htmlTag.append(cardHtml);
    });

    $("#toBringChecklists").hide();
    $("#addToBring").hide();
    $("#toBringExpanded").show();
}

$(document).ready(function () {
    //Section for To-Do Checklists
    loadToDo();
    componentHandler.upgradeDom();
    var toDoItems = 0;


    $("#addToDo").on("click", function () {
        toDoListAddHeaders();
        addActivity();
        $("#toDoChecklists").hide();
        $("#addToDo").hide();
        $("#emptyAList").hide();
        $("#toDoWindow").show();
    });

    $("#addActivity").on("click", function () {
        addActivity();
        $('.mdl-tooltip.is-active').removeClass('is-active');
    });

    $("#saveToDo").on("click", function () {
        saveToDo();
        $('.mdl-tooltip.is-active').removeClass('is-active');
    });

    $("#closeToDo").on("click", function () {
        closeToDo();
        $('.mdl-tooltip.is-active').removeClass('is-active');
    });

    $(".closeToDoChecklist").on("click", function (event) {
        $("#toDoExpandedList").html(""); //resets the div
        $("#toDoExpanded").hide();
        $("#toDoChecklists").show();
        $("#addToDo").show();
    });

    function toDoListAddHeaders() {
        var headerTemplate = '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label"><input class="mdl-textfield__input" type="text" id="tdchName" required><label class="mdl-textfield__label" for="tdchName">Name your checklist</label></div>';
        $("#activityList").append(headerTemplate);

        headerTemplate = '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label"><input class="mdl-textfield__input" type="text" id="tdchDescription" required><label class="mdl-textfield__label" for="tdchDescription">Type a small description for your checklist</label></div>';
        $("#activityList").append(headerTemplate);
        componentHandler.upgradeDom();
    }

    function addActivity() {
        toDoItems++;
        var newActivityTemplate = '<div class="mdl-textfield mdl-js-textfield"><input class="mdl-textfield__input tdcItem" type="text" id="activityName' + toDoItems + '"><label class="mdl-textfield__label" for="activityName' + toDoItems + '">Activity #' + toDoItems + '</label></div>';
        $("#activityList").append(newActivityTemplate);

        newActivityTemplate = '<div class="mdl-textfield mdl-js-textfield"><input class="mdl-textfield__input tdcNotes" type="text" id="itemNotes' + toDoItems + '"><label class="mdl-textfield__label" for="itemNotes' + toDoItems + '">Notes</label></div>';
        $("#activityList").append(newActivityTemplate);
        componentHandler.upgradeDom();
    }

    function saveToDo() {
        var itemList = [];
        var valid = true;

        if ($("#tdchName").val() == "") {
            valid = false;
        }
        if ($("#tdchDescription").val() == "") {
            valid = false;
        }

        if (!valid) {
            alert("Please give your checklist a name and a description!");
        }
        else {
            var itemNumber = 0;
            $(".tdcItem").each(function () {
                if ($(this).val() != "") {
                    itemNumber++;
                }

                itemList.push({
                    "name": $(this).val(),
                    "quantity": 0, //intended, to-dos have no quantities
                    "notes": "", //modified in next loop
                });
            });

            if (itemNumber > 0) {
                $(".tdcNotes").each(function (key, noteValue) {
                    itemList[key]["notes"] = noteValue.value;
                });

                //removes empty elements
                var i = itemList.length
                while (i--) {
                    if (itemList[i]["name"] == "") {
                        itemList.splice(i, 1);
                    }
                }

                var jsonData = {
                    "action": "SAVECHECKLIST",
                    "checklistType": "ToDo",
                    "checklistDescription": $("#tdchDescription").val(),
                    "checklistName": $("#tdchName").val(),
                    "activityItems": itemList,
                    "username": $("#username").html()
                };

                $.ajax({
                    url: "data/applicationLayer.php",
                    type: "POST",
                    data: jsonData,
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    success: function (jsonResponse) {
                        var newCardData = {
                            "id": jsonResponse["checklistId"],
                            "checklistName": jsonData["checklistName"],
                            "checklistDescription": jsonData["checklistDescription"]
                        }

                        addToDoCardToDOM(newCardData, $("#toDoChecklists"), false);
                        closeToDo();
                        componentHandler.upgradeDom();
                    },
                    error: function (errorMessage) {
                        alert(errorMessage.responseText);
                    }
                });

                resetChecklist();
            }
            else {
                alert("Please write at least one activity!");
            }
        }
    }

    function loadToDo() {
        var jsonData = {
            "action": "LOADCHECKLISTS",
            "checklistType": "ToDo",
            "username": $("#username").html()
        };

        $.ajax({
            url: "data/applicationLayer.php",
            type: "POST",
            data: jsonData,
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            success: function (jsonResponse) {
                loadToDoCards(jsonResponse);
            },
            error: function (errorMessage) {
                alert(errorMessage.responseText);
            }
        });
    }

    function loadToDoCards(toDoChecklists) {
        var htmlTag = $("#toDoChecklists");
        $.each(toDoChecklists, function (key, value) {
            addToDoCardToDOM(value, htmlTag, true);
        });
        componentHandler.upgradeDom();
    }

    function addToDoCardToDOM(value, htmlTag, append) {
        $("#emptyAList").hide();
        var card = '<div class="card" id="tdc' + value["id"] + '" style="margin: 0 auto;"><div class="mdl-card-square mdl-card mdl-shadow--4dp"><div class="mdl-card__title mdl-card--expand"><h2 class="mdl-card__title-text title">' + value["checklistName"] + '</h2></div><div class="mdl-card__supporting-text description">' + value["checklistDescription"] + '</div><div class="mdl-card__actions mdl-card--border"><p class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect openToDoChecklist" id="tdco' + value["id"] + '" onclick="openToDoOnClick(' + value["id"] + ',\'' + value["checklistName"] + '\',\'' + value["checklistDescription"] + '\')">Open Checklist</p></div><div class="mdl-card__menu"></div></div></div>';
        if (append) {
            htmlTag.append(card);
        }
        else {
            htmlTag.prepend(card);
        }
    }

    function closeToDo() {
        $("#toDoWindow").hide();
        $("#addToDo").show();
        $("#toDoChecklists").show();
        console.log($("#toDoChecklists").html());
        if ($("#toDoChecklists").html() == "") {
            $("#emptyAList").show();
        }
        resetChecklist();
    }

    function closeAddToDo() {
        $("#toDoWindow").hide();
        $("#addToDo").show();
        $("#toDoChecklists").show();
        if ($("#toDoChecklists").html() == "") {
            $("#emptyAList").show();
        }
        resetChecklist();
    }

    function resetChecklist() {
        toDoItems = 0;
        $("#activityList").html("");

        toBringItems = 0;
        $("#itemList").html("");
    }

    //Section for To-Bring Checklists
    loadToBring();
    componentHandler.upgradeDom();
    var toBringItems = 0;

    $("#addToBring").on("click", function () {
        toBringListAddHeaders();
        addItem();
        $("#toBringChecklists").hide();
        $("#addToBring").hide();
        $("#emptyIList").hide();
        $("#toBringWindow").show();
    });

    $("#addItem").on("click", function () {
        addItem();
        $('.mdl-tooltip.is-active').removeClass('is-active');
    });

    $("#saveToBring").on("click", function () {
        saveToBring();
        $('.mdl-tooltip.is-active').removeClass('is-active');
    });

    $("#closeToBring").on("click", function () {
        closeToBring();
        $('.mdl-tooltip.is-active').removeClass('is-active');
    });

    $(".closeToBringChecklist").on("click", function (event) {
        $("#toBringExpandedList").html(""); //resets the div
        $("#toBringExpanded").hide();
        $("#toBringChecklists").show();
        $("#addToBring").show();
    });

    function toBringListAddHeaders() {
        var headerTemplate = '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label"><input class="mdl-textfield__input" type="text" id="tbchName" required><label class="mdl-textfield__label" for="tbchName">Name your checklist</label></div>';
        $("#itemList").append(headerTemplate);

        headerTemplate = '<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label"><input class="mdl-textfield__input" type="text" id="tbchDescription" required><label class="mdl-textfield__label" for="tbchDescription">Type a small description for your checklist</label></div>';
        $("#itemList").append(headerTemplate);
        componentHandler.upgradeDom();
    }

    function addItem() {
        toBringItems++;
        var newItemTemplate = '<div class="mdl-textfield mdl-js-textfield"><input class="mdl-textfield__input tbcItem" type="text" id="itemName' + toBringItems + '"><label class="mdl-textfield__label" for="itemName' + toBringItems + '">Item #' + toBringItems + '</label></div>';
        $("#itemList").append(newItemTemplate);

        newItemTemplate = '<div class="mdl-textfield mdl-js-textfield"><input class="mdl-textfield__input tbcNotes" type="text" id="itemNotes' + toBringItems + '"><label class="mdl-textfield__label" for="itemNotes' + toBringItems + '">Notes</label></div>';
        $("#itemList").append(newItemTemplate);
        componentHandler.upgradeDom();
    }

    function saveToBring() {
        var itemList = [];
        var valid = true

        if ($("#tbchName").val() == "") {
            valid = false;
        }
        if ($("#tbchDescription").val() == "") {
            valid = false;
        }

        if (!valid) {
            alert("Please give your checklist a name and a description!");
        }
        else {
            var itemNumber = 0;
            $(".tbcItem").each(function () {
                if ($(this).val() != "") {
                    itemNumber++;
                }
                itemList.push({
                    "name": $(this).val(),
                    "quantity": 0, //intended, to-dos have no quantities
                    "notes": "", //modified in next loop
                });
            });

            if (itemNumber > 0) {
                $(".tbcNotes").each(function (key, noteValue) {
                    itemList[key]["notes"] = noteValue.value;
                });

                //removes empty elements
                var i = itemList.length
                while (i--) {
                    if (itemList[i]["name"] == "") {
                        itemList.splice(i, 1);
                    }
                }

                var jsonData = {
                    "action": "SAVECHECKLIST",
                    "checklistType": "ToBring",
                    "checklistDescription": $("#tbchDescription").val(),
                    "checklistName": $("#tbchName").val(),
                    "activityItems": itemList,
                    "username": $("#username").html()
                };

                $.ajax({
                    url: "data/applicationLayer.php",
                    type: "POST",
                    data: jsonData,
                    dataType: "json",
                    contentType: "application/x-www-form-urlencoded",
                    success: function (jsonResponse) {
                        var newCardData = {
                            "id": jsonResponse["checklistId"],
                            "checklistName": jsonData["checklistName"],
                            "checklistDescription": jsonData["checklistDescription"]
                        }

                        addToBringCardToDOM(newCardData, $("#toBringChecklists"), false);
                        closeToBring();
                        componentHandler.upgradeDom();
                    },
                    error: function (errorMessage) {
                        alert(errorMessage.responseText);
                    }
                });

                resetChecklist();
            }
            else {
                alert("Please write at least one item!");
            }
        }
    }

    function closeToBring() {
        $("#toBringWindow").hide();
        $("#addToBring").show();
        $("#toBringChecklists").show();
        if ($("#toBringChecklists").html() == "") {
            $("#emptyIList").show();
        }
        resetChecklist();
    }

    function closeAddToBring() {
        $("#toBringWindow").hide();
        $("#addToBring").hide();
        $("#toBringChecklists").show();
        if ($("#toBringChecklists").html() == "") {
            $("#emptyIList").show();
        }
        resetChecklist();
    }

    function loadToBring() {
        var jsonData = {
            "action": "LOADCHECKLISTS",
            "checklistType": "ToBring",
            "username": $("#username").html()
        };

        $.ajax({
            url: "data/applicationLayer.php",
            type: "POST",
            data: jsonData,
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            success: function (jsonResponse) {
                loadToBringCards(jsonResponse);
            },
            error: function (errorMessage) {
                alert(errorMessage.responseText);
            }
        });
    }

    function loadToBringCards(toBringChecklists) {
        var htmlTag = $("#toBringChecklists");
        $.each(toBringChecklists, function (key, value) {
            addToBringCardToDOM(value, htmlTag, true);
        });
        componentHandler.upgradeDom();
    }

    function addToBringCardToDOM(value, htmlTag, append) {
        $("#emptyIList").hide();
        var card = '<div class="card" id="tbc' + value["id"] + '" style="margin: 0 auto;"><div class="mdl-card-square mdl-card mdl-shadow--4dp"><div class="mdl-card__title mdl-card--expand"><h2 class="mdl-card__title-text title">' + value["checklistName"] + '</h2></div><div class="mdl-card__supporting-text description">' + value["checklistDescription"] + '</div><div class="mdl-card__actions mdl-card--border"><p class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect openToBringChecklist" id="tbco' + value["id"] + '" onclick="openToBringOnClick(' + value["id"] + ',\'' + value["checklistName"] + '\',\'' + value["checklistDescription"] + '\')">Open Checklist</p></div><div class="mdl-card__menu"></div></div></div>';

        if (append) {
            htmlTag.append(card);
        }
        else {
            htmlTag.prepend(card);
        }
    }
});
