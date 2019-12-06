'use strict';
(function ($) {
    const ownAcronym = document.getElementById('own-avatar-acronym').innerText;

    let aliveInterval;

    const createRequest = (data) => {
        const request = new XMLHttpRequest();
        if (! data.async) {
            data.async = true;
        }
        if (! data.body) {
            data.body = null;
        }
        request.onreadystatechange = function () {
            if (this.readyState === 4 && this.status !== 500) {
                try {
                    data.success(JSON.parse(request.responseText));
                } catch (e) {
                    data.success(request.responseText);
                }
            }
        };
        request.open(data.type, data.target, data.async);
        request.setRequestHeader('Content-Type', 'application/json');
        request.send(JSON.stringify(data.body));
    };

    const showModal = (result) => {
        let body = '<p>Please, select the user which you want to start a chat!</p>\n' +
            '<select class="form-control" id="new-chat-users"><option value="-1">Select the user to chat with!</option>';
        result.forEach((row) => {
            body += '<option value="'+row['id']+'" data-acronym="' + row['first_name'].charAt(0) + row['last_name'].charAt(0) + '">'+ row['first_name'] + ' ' + row['last_name'] + '</option>';
        });
        body += '</select>';
        BootstrapDialog.show({
            title: 'Begin new chat',
            message: body,
            closable: false,
            buttons: [
                {
                    label: 'Start Chat',
                    cssClass: 'btn-primary pull-left',
                    action: (self) => {
                        const select = document.getElementById('new-chat-users');
                        if (select.value === "-1") {
                            return BootstrapDialog.show({
                                title: 'User not selected',
                                message: 'You have to select a user to begin a new chat',
                                closable: true
                            });
                        }

                        const option = select.options[select.selectedIndex];
                        createRequest({
                           type: 'POST',
                           target: '/chat',
                           body: {
                               receiver: select.value
                           },
                           success: (response) => {
                               generateNewChat({
                                   chatId: response,
                                   userId: select.value,
                                   name: option.innerText,
                                   acronym: option.getAttribute('data-acronym')
                               });
                               self.close()
                           }
                        });
                    }
                },
                {
                    label: 'Dismiss',
                    cssClass: 'btn-default pull-right',
                    action: (self) => self.close()
                }
            ]
        });
    };

    const generateNewChat = (loader) => {
        const template = '<div class="channel active open-chat" data-loader="' + loader.chatId + '" data-colour="bg-success">\n' +
            '<span class="avatar avatar-sm mr-3 bg-success rounded-circle">' + loader.acronym + '</span>\n' +
            '<div class="flex-grow-1">\n' +
            '<div class="d-flex align-items-center mb-3">\n' +
            '<h6 class="mr-auto">' + loader.name + '</h6>\n' +
            '</div>\n' +
            '<p class="text">\n' +
            '</p>\n' +
            '</div>\n' +
            '</div>';
        document.getElementById('new-chat').insertAdjacentHTML('afterend', template);
        openChat(loader);
    };

    const openChat = (loader) => {
        const header = '<div class="chat-header">\n' +
            '<span class="avatar avatar-sm mr-3 bg-info rounded-circle">' + loader.acronym + '</span>\n' +
            '<h6 class="lh-100">' + loader.name + '</h6>\n' +
            '</div>\n';
        const body = '<div class="chat-body" id="chat-window-body">\n' +
            '</div>\n';
        const footer = '<div class="chat-footer">\n' +
            '<form id="submit-form">\n' +
            '<div class="input-group">\n' +
            '<input class="form-control form-control-lg" placeholder="Type message..." autofocus id="message-field" style="color: #495057">\n' +
            '<div class="input-group-append">\n' +
            '<button class="btn" type="submit">\n' +
            '<i class="fa fa-paper-plane-o"></i>\n' +
            '</button>\n' +
            '</div>\n' +
            '</div>\n' +
            '</form>\n' +
            '</div>';
        document.getElementById('chat-window').innerHTML = header + body + footer;
        clearInterval(aliveInterval);
        aliveInterval = setInterval(() => createRequest({
            type: 'GET',
            target: '/chat/' + loader.chatId + '/update',
            success: (result) => loadMessages({
                messages: result,
                acronym: loader.acronym
            })
        }), 1000);
        document.getElementById('submit-form').onsubmit = () => {
            const messageField = document.getElementById('message-field');
            createRequest({
                type: 'PUT',
                target: '/chat/'+loader.chatId,
                body: {
                    message: messageField.value
                },
                success: () => {
                    loadMessages({
                        messages: [
                            {'message': messageField.value, 'owner_user': true}
                        ]
                    });
                    messageField.value = "";
                }
            });
            return false;
        };

        loadMessages({
            messages: loader.messages,
            acronym: loader.acronym
        });
    };

    const loadMessages = (data) => {
        if (! data.messages || !data.messages.length) {
            return;
        }
        const chatBody = document.getElementById('chat-window-body');
        const ownMessageTemplate = '<div class="d-flex align-items-end justify-content-end">\n' +
            '<span class="avatar avatar-sm order-2 ml-3 bg-primary rounded-circle">{{acronym}}</span>\n' +
            '<div class="card mb-3 text-white bg-primary">\n' +
            '{{message_body}}\n' +
            '</div>\n' +
            '</div>\n';

        const otherMessageTemplate = '<div class="d-flex align-items-end mb-5">\n' +
            '<span class="avatar avatar-sm mr-3 bg-info rounded-circle">{{acronym}}</span>\n' +
            '<div class="card mb-3">\n' +
            '{{message_body}}\n' +
            '</div>\n' +
            '</div>\n';

        data.messages.forEach((message) => {
            const [targetBody, targetAcronym] = message['owner_user'] ? [ownMessageTemplate, ownAcronym] : [otherMessageTemplate, data.acronym];
            chatBody.innerHTML += targetBody.replace('{{acronym}}', targetAcronym).replace('{{message_body}}', message['message']);
        });
        chatBody.scrollTop = chatBody.scrollHeight;
    };

    const addEventToOption = (option) => {
        document.querySelectorAll('.channel.open-chat').forEach((option) => option.classList.remove('active'));
        option.classList.add('active');
        option.onclick = () => createRequest({
            type: 'GET',
            target: '/chat/'+option.getAttribute('data-loader'),
            success: (result) => openChat(result)
        })
    };

    document.querySelector('.channel.add-new').onclick = () => createRequest({
        type: 'GET',
        target: '/users/available',
        success: (result) => showModal(result)
    });

    document.querySelectorAll('.channel.open-chat').forEach((option) => addEventToOption(option));
})($);