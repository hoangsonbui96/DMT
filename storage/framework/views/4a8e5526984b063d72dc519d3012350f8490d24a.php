<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo $__env->yieldContent('title'); ?></title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .code {
                border-right: 2px solid;
                font-size: 26px;
                padding: 0 15px 0 15px;
                text-align: center;
            }

            .message {
                font-size: 18px;
                text-align: center;
            }

            .counter {
                position: fixed;
                top: 59%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
            }

            .counter.hide {
                transform: translate(-50%, -50%) scale(0);
                animation: hide .2s ease-out;
            }

            .nums {
                color: #3498db;
                position: relative;
                font-size: 18px;
                overflow: hidden;
                width: 250px;
                height: 22px;
            }

            .nums span {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%) rotate(120deg);
                transform-origin: bottom center;
            }

            .nums span.in {
                transform: translate(-50%, -50%) rotate(0deg);
                animation: goIn .5s ease-in-out;
            }

            .nums span.out {
                animation: goOut .5s ease-in-out;
            }

            @keyframes  goIn {
                0% {
                    transform: translate(-50%, -50%) rotate(120deg);
                }
                30% {
                    transform: translate(-50%, -50%) rotate(-20deg);
                }

                60% {
                    transform: translate(-50%, -50%) rotate(10deg);
                }

                90%, 100% {
                    transform: translate(-50%, -50%) rotate(0deg);
                }

            }

            @keyframes  goOut {
                0%, 30% {
                    transform: translate(-50%, -50%) rotate(0deg);
                }

                60% {
                    transform: translate(-50%, -50%) rotate(20deg);
                }

                100% {
                    transform: translate(-50%, -50%) rotate(-120deg);
                }
            }

            .counter h5 {
                margin: 5px;
                text-transform: uppercase;
            }
        </style>
    </head>
    <body>
        <div class="counter">
            <div class="nums"></div>
            <h5>Redirecting...</h5>
        </div>

        <div class="flex-center position-ref full-height">
            <div class="code">
                <?php echo $__env->yieldContent('code'); ?>
            </div>

            <div class="message" style="padding: 10px;">
                <?php echo $__env->yieldContent('message'); ?>
            </div>
        </div>

        <script>
            const SECOND = 3;

            let html = ''
            for(let i = SECOND; i >= 0; i--) {
                if(i == SECOND) {
                    html += `<span class="in">${i}</span>`;
                } else {
                    html += `<span>${i}</span>`;
                }
            }
            document.querySelector('.nums').innerHTML = html;

            const nums = document.querySelectorAll('.nums span');
            const counter = document.querySelector('.counter');

            runAnimation();

            function runAnimation() {
                nums.forEach((num, idx) => {
                    const penultimate = nums.length - 1;
                    num.addEventListener('animationend', (e) => {
                        if(e.animationName === 'goIn' && idx !== penultimate) {
                            num.classList.remove('in');
                            num.classList.add('out');
                        } else if (e.animationName === 'goOut' && num.nextElementSibling) {
                            num.nextElementSibling.classList.add('in');
                        } else {
                            window.location.href = '<?php echo e(route('admin.home')); ?>';
                        }
                    });
                });
            }
        </script>
    </body>
</html>
<?php /**PATH D:\DMT\resources\views/errors/minimal.blade.php ENDPATH**/ ?>