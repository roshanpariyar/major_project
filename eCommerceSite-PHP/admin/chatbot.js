document.addEventListener('DOMContentLoaded', function() {
    var botui = new BotUI('botui-app');

    botui.message.bot({
        delay: 500,
        content: 'Hello! I am here to help you with using this website.'
    }).then(function() {
        return botui.message.bot({
            delay: 1000,
            content: 'You can start by logging in or registering a new account.'
        });
    }).then(function() {
        return botui.action.button({
            delay: 1000,
            action: [
                {
                    text: 'How do I log in?',
                    value: 'login'
                },
                {
                    text: 'How do I search for products?',
                    value: 'search'
                },
                {
                    text: 'How do I add products to the cart?',
                    value: 'add_to_cart'
                },
                {
                    text: 'How do I proceed to checkout?',
                    value: 'checkout'
                },
                {
                    text: 'How do I edit my profile?',
                    value: 'edit_profile'
                }
            ]
        });
    }).then(function(res) {
        var message;

        switch(res.value) {
            case 'login':
                message = 'To log in, click on the "Login" button at the top right corner and enter your credentials.';
                break;
            case 'search':
                message = 'To search for products, use the search bar at the top and type in the product name or category.';
                break;
            case 'add_to_cart':
                message = 'To add products to the cart, browse the products, and click on the "Add to Cart" button for the items you want.';
                break;
            case 'checkout':
                message = 'To proceed to checkout, go to your cart and click on the "Checkout" button. Fill in your billing and shipping details, then complete the payment.';
                break;
            case 'edit_profile':
                message = 'To edit your profile, go to the profile section and update your information, then save the changes.';
                break;
        }

        return botui.message.bot({
            delay: 1000,
            content: message
        });
    }).then(function() {
        return botui.message.bot({
            delay: 1000,
            content: 'If you have any other questions, feel free to ask!'
        });
    });
});
