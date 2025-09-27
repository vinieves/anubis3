<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Payment</title>
    <link rel="stylesheet" crossorigin="" href="{{ asset('assets/css/checkout.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">
        :where(html[dir="ltr"]),
        :where([data-sonner-toaster][dir="ltr"]) {
            --toast-icon-margin-start: -3px;
            --toast-icon-margin-end: 4px;
            --toast-svg-margin-start: -1px;
            --toast-svg-margin-end: 0px;
            --toast-button-margin-start: auto;
            --toast-button-margin-end: 0;
            --toast-close-button-start: 0;
            --toast-close-button-end: unset;

            --toast-close-button-transform: translate(-35%, -35%)}:where(html[dir="rtl"]),
            :where([data-sonner-toaster][dir="rtl"]) {
                --toast-icon-margin-start: 4px;
                --toast-icon-margin-end: -3px;
                --toast-svg-margin-start: 0px;
                --toast-svg-margin-end: -1px;
                --toast-button-margin-start: 0;
                --toast-button-margin-end: auto;
                --toast-close-button-start: unset;
                --toast-close-button-end: 0;
                --toast-close-button-transform: translate(35%, -35%)
            }

            :where([data-sonner-toaster]) {
                position: fixed;
                width: var(--width);
                font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
                --gray1: hsl(0, 0%, 99%);
                --gray2: hsl(0, 0%, 97.3%);
                --gray3: hsl(0, 0%, 95.1%);
                --gray4: hsl(0, 0%, 93%);
                --gray5: hsl(0, 0%, 90.9%);
                --gray6: hsl(0, 0%, 88.7%);
                --gray7: hsl(0, 0%, 85.8%);
                --gray8: hsl(0, 0%, 78%);
                --gray9: hsl(0, 0%, 56.1%);
                --gray10: hsl(0, 0%, 52.3%);
                --gray11: hsl(0, 0%, 43.5%);
                --gray12: hsl(0, 0%, 9%);

                --border-radius: 8px;box-sizing:border-box;padding:0;margin:0;list-style:none;outline:none;z-index:999999999}:where([data-sonner-toaster][data-x-position="right"]) {
                    right: max(var(--offset), env(safe-area-inset-right))
                }

                :where([data-sonner-toaster][data-x-position="left"]) {
                    left: max(var(--offset), env(safe-area-inset-left))
                }

                :where([data-sonner-toaster][data-x-position="center"]) {
                    left: 50%;
                    transform: translate(-50%)
                }

                :where([data-sonner-toaster][data-y-position="top"]) {
                    top: max(var(--offset), env(safe-area-inset-top))
                }

                :where([data-sonner-toaster][data-y-position="bottom"]) {
                    bottom: max(var(--offset), env(safe-area-inset-bottom))
                }

                :where([data-sonner-toast]) {
                    --y: translateY(100%);
                    --lift-amount: calc(var(--lift) * var(--gap));
                    z-index: var(--z-index);
                    position: absolute;
                    opacity: 0;
                    transform: var(--y);
                    filter: blur(0);
                    touch-action: none;

                    transition: transform .4s, opacity .4s, height .4s, box-shadow .2s;box-sizing:border-box;outline:none;overflow-wrap:anywhere}:where([data-sonner-toast][data-styled="true"]) {
                        padding: 16px;
                        background: var(--normal-bg);
                        border: 1px solid var(--normal-border);
                        color: var(--normal-text);
                        border-radius: var(--border-radius);
                        box-shadow: 0 4px 12px #0000001a;
                        width: var(--width);
                        font-size: 13px;
                        display: flex;
                        align-items: center;
                        gap: 6px
                    }

                    :where([data-sonner-toast]:focus-visible) {
                        box-shadow: 0 4px 12px #0000001a, 0 0 0 2px #0003}:where([data-sonner-toast][data-y-position="top"]) {
                            top: 0;
                            --y: translateY(-100%);
                            --lift: 1;

                            --lift-amount: calc(1 * var(--gap))}:where([data-sonner-toast][data-y-position="bottom"]) {
                                bottom: 0;
                                --y: translateY(100%);
                                --lift: -1;
                                --lift-amount: calc(var(--lift) * var(--gap))
                            }

                            :where([data-sonner-toast]) :where([data-description]) {
                                font-weight: 400;
                                line-height: 1.4;
                                color: inherit
                            }

                            :where([data-sonner-toast]) :where([data-title]) {
                                font-weight: 500;
                                line-height: 1.5;
                                color: inherit
                            }

                            :where([data-sonner-toast]) :where([data-icon]) {
                                display: flex;
                                height: 16px;
                                width: 16px;
                                position: relative;
                                justify-content: flex-start;
                                align-items: center;
                                flex-shrink: 0;
                                margin-left: var(--toast-icon-margin-start);
                                margin-right: var(--toast-icon-margin-end)
                            }

                            :where([data-sonner-toast][data-promise="true"]) :where([data-icon])>svg {
                                opacity: 0;
                                transform: scale(.8);
                                transform-origin: center;
                                animation: sonner-fade-in .3s ease forwards
                            }

                            :where([data-sonner-toast]) :where([data-icon])>* {
                                flex-shrink: 0
                            }

                            :where([data-sonner-toast]) :where([data-icon]) svg {
                                margin-left: var(--toast-svg-margin-start);
                                margin-right: var(--toast-svg-margin-end)
                            }

                            :where([data-sonner-toast]) :where([data-content]) {
                                display: flex;
                                flex-direction: column;
                                gap: 2px
                            }

                            [data-sonner-toast][data-styled=true] [data-button] {
                                border-radius: 4px;
                                padding-left: 8px;
                                padding-right: 8px;
                                height: 24px;
                                font-size: 12px;
                                color: var(--normal-bg);
                                background: var(--normal-text);
                                margin-left: var(--toast-button-margin-start);
                                margin-right: var(--toast-button-margin-end);
                                border: none;
                                cursor: pointer;
                                outline: none;
                                display: flex;
                                align-items: center;
                                flex-shrink: 0;
                                transition: opacity .4s, box-shadow .2s
                            }

                            :where([data-sonner-toast]) :where([data-button]):focus-visible {
                                box-shadow: 0 0 0 2px #0006
                            }

                            :where([data-sonner-toast]) :where([data-button]):first-of-type {
                                margin-left: var(--toast-button-margin-start);
                                margin-right: var(--toast-button-margin-end)
                            }

                            :where([data-sonner-toast]) :where([data-cancel]) {
                                color: var(--normal-text);
                                background: rgba(0, 0, 0, .08)
                            }

                            :where([data-sonner-toast][data-theme="dark"]) :where([data-cancel]) {
                                background: rgba(255, 255, 255, .3)
                            }

                            :where([data-sonner-toast]) :where([data-close-button]) {
                                position: absolute;
                                left: var(--toast-close-button-start);
                                right: var(--toast-close-button-end);
                                top: 0;
                                height: 20px;
                                width: 20px;
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                padding: 0;
                                background: var(--gray1);
                                color: var(--gray12);
                                border: 1px solid var(--gray4);
                                transform: var(--toast-close-button-transform);
                                border-radius: 50%;
                                cursor: pointer;
                                z-index: 1;
                                transition: opacity .1s, background .2s, border-color .2s
                            }

                            :where([data-sonner-toast]) :where([data-close-button]):focus-visible {
                                box-shadow: 0 4px 12px #0000001a, 0 0 0 2px #0003
                            }

                            :where([data-sonner-toast]) :where([data-disabled="true"]) {
                                cursor: not-allowed
                            }

                            :where([data-sonner-toast]):hover :where([data-close-button]):hover {
                                background: var(--gray2);
                                border-color: var(--gray5)
                            }

                            :where([data-sonner-toast][data-swiping="true"]):before {
                                content: "";
                                position: absolute;
                                left: 0;
                                right: 0;
                                height: 100%;
                                z-index: -1
                            }

                            :where([data-sonner-toast][data-y-position="top"][data-swiping="true"]):before {
                                bottom: 50%;

                                transform: scaleY(3) translateY(50%)}:where([data-sonner-toast][data-y-position="bottom"][data-swiping="true"]):before {
                                    top: 50%;

                                    transform: scaleY(3) translateY(-50%)}:where([data-sonner-toast][data-swiping="false"][data-removed="true"]):before {
                                        content: "";
                                        position: absolute;
                                        inset: 0;
                                        transform: scaleY(2)
                                    }

                                    :where([data-sonner-toast]):after {
                                        content: "";
                                        position: absolute;
                                        left: 0;

                                        height:calc(var(--gap) + 1px);bottom:100%;width:100%}:where([data-sonner-toast][data-mounted="true"]) {
                                            --y: translateY(0);opacity:1}:where([data-sonner-toast][data-expanded="false"][data-front="false"]) {
                                                --scale: var(--toasts-before) * .05 + 1;
                                                --y: translateY(calc(var(--lift-amount) * var(--toasts-before))) scale(calc(-1 * var(--scale)));
                                                height: var(--front-toast-height)
                                            }

                                            :where([data-sonner-toast])>* {
                                                transition: opacity .4s}:where([data-sonner-toast][data-expanded="false"][data-front="false"][data-styled="true"])>* {
                                                    opacity: 0
                                                }

                                                :where([data-sonner-toast][data-visible="false"]) {
                                                    opacity: 0;
                                                    pointer-events: none
                                                }

                                                :where([data-sonner-toast][data-mounted="true"][data-expanded="true"]) {
                                                    --y: translateY(calc(var(--lift) * var(--offset)));height:var(--initial-height)}:where([data-sonner-toast][data-removed="true"][data-front="true"][data-swipe-out="false"]) {
                                                            --y: translateY(calc(var(--lift) * -100%));opacity:0}:where([data-sonner-toast][data-removed="true"][data-front="false"][data-swipe-out="false"][data-expanded="true"]) {
                                                                    --y: translateY(calc(var(--lift) * var(--offset) + var(--lift) * -100%));opacity:0}:where([data-sonner-toast][data-removed="true"][data-front="false"][data-swipe-out="false"][data-expanded="false"]) {
                                                                            --y: translateY(40%); opacity:0; transition:transform .5s, opacity .2s}:where([data-sonner-toast][data-removed="true"][data-front="false"]):before {
                                                                            height:calc(var(--initial-height) + 20%)
                                                                        }

                                                                        [data-sonner-toast][data-swiping=true] {
                                                                            transform:var(--y) translateY(var(--swipe-amount, 0px)); transition:none
                                                                        }

                                                                        [data-sonner-toast][data-swipe-out=true][data-y-position=bottom], [data-sonner-toast][data-swipe-out=true][data-y-position=top] {
                                                                            animation:swipe-out .2s ease-out forwards
                                                                        }

                                                                        @keyframes swipe-out {
                                                                            0% {
                                                                                transform:translateY(calc(var(--lift) * var(--offset) + var(--swipe-amount))); opacity:1
                                                                            }

                                                                            to {
                                                                                transform:translateY(calc(var(--lift) * var(--offset) + var(--swipe-amount) + var(--lift) * -100%)); opacity:0
                                                                            }
                                                                        }

                                                                        @media (max-width: 600px) {
                                                                            [data-sonner-toaster] {
                                                                                position:fixed; --mobile-offset: 16px; right:var(--mobile-offset); left:var(--mobile-offset); width:100%
                                                                            }

                                                                            [data-sonner-toaster] [data-sonner-toast] {
                                                                                left:0; right:0; width:calc(100% - var(--mobile-offset) * 2)
                                                                            }

                                                                            [data-sonner-toaster][data-x-position=left] {
                                                                                left:var(--mobile-offset)
                                                                            }

                                                                            [data-sonner-toaster][data-y-position=bottom] {
                                                                                bottom:20px
                                                                            }

                                                                            [data-sonner-toaster][data-y-position=top] {
                                                                                top:20px
                                                                            }

                                                                            [data-sonner-toaster][data-x-position=center] {
                                                                                left:var(--mobile-offset); right:var(--mobile-offset); transform:none
                                                                            }
                                                                        }

                                                                        [data-sonner-toaster][data-theme=light] {
                                                                            --normal-bg: #fff; --normal-border: var(--gray4); --normal-text: var(--gray12); --success-bg: hsl(143, 85%, 96%); --success-border: hsl(145, 92%, 91%); --success-text: hsl(140, 100%, 27%); --info-bg: hsl(208, 100%, 97%); --info-border: hsl(221, 91%, 91%); --info-text: hsl(210, 92%, 45%); --warning-bg: hsl(49, 100%, 97%); --warning-border: hsl(49, 91%, 91%); --warning-text: hsl(31, 92%, 45%); --error-bg: hsl(359, 100%, 97%); --error-border: hsl(359, 100%, 94%); --error-text: hsl(360, 100%, 45%)
                                                                        }

                                                                        [data-sonner-toaster][data-theme=light] [data-sonner-toast][data-invert=true] {
                                                                            --normal-bg: #000; --normal-border: hsl(0, 0%, 20%); --normal-text: var(--gray1)
                                                                        }

                                                                        [data-sonner-toaster][data-theme=dark] [data-sonner-toast][data-invert=true] {
                                                                            --normal-bg: #fff; --normal-border: var(--gray3); --normal-text: var(--gray12)
                                                                        }

                                                                        [data-sonner-toaster][data-theme=dark] {
                                                                            --normal-bg: #000; --normal-border: hsl(0, 0%, 20%); --normal-text: var(--gray1); --success-bg: hsl(150, 100%, 6%); --success-border: hsl(147, 100%, 12%); --success-text: hsl(150, 86%, 65%); --info-bg: hsl(215, 100%, 6%); --info-border: hsl(223, 100%, 12%); --info-text: hsl(216, 87%, 65%); --warning-bg: hsl(64, 100%, 6%); --warning-border: hsl(60, 100%, 12%); --warning-text: hsl(46, 87%, 65%); --error-bg: hsl(358, 76%, 10%); --error-border: hsl(357, 89%, 16%); --error-text: hsl(358, 100%, 81%)
                                                                        }

                                                                        [data-rich-colors=true][data-sonner-toast][data-type=success], [data-rich-colors=true][data-sonner-toast][data-type=success] [data-close-button] {
                                                                            background:var(--success-bg); border-color:var(--success-border); color:var(--success-text)
                                                                        }

                                                                        [data-rich-colors=true][data-sonner-toast][data-type=info], [data-rich-colors=true][data-sonner-toast][data-type=info] [data-close-button] {
                                                                            background:var(--info-bg); border-color:var(--info-border); color:var(--info-text)
                                                                        }

                                                                        [data-rich-colors=true][data-sonner-toast][data-type=warning], [data-rich-colors=true][data-sonner-toast][data-type=warning] [data-close-button] {
                                                                            background:var(--warning-bg); border-color:var(--warning-border); color:var(--warning-text)
                                                                        }

                                                                        [data-rich-colors=true][data-sonner-toast][data-type=error], [data-rich-colors=true][data-sonner-toast][data-type=error] [data-close-button] {
                                                                            background:var(--error-bg); border-color:var(--error-border); color:var(--error-text)
                                                                        }

                                                                        .sonner-loading-wrapper {
                                                                            --size: 16px; height:var(--size); width:var(--size); position:absolute; inset:0; z-index:10
                                                                        }

                                                                        .sonner-loading-wrapper[data-visible=false] {
                                                                            transform-origin:center; animation:sonner-fade-out .2s ease forwards
                                                                        }

                                                                        .sonner-spinner {
                                                                            position:relative; top:50%; left:50%; height:var(--size); width:var(--size)
                                                                        }

                                                                        .sonner-loading-bar {
                                                                            animation:sonner-spin 1.2s linear infinite; background:var(--gray11); border-radius:6px; height:8%; left:-10%; position:absolute; top:-3.9%; width:24%
                                                                        }

                                                                        .sonner-loading-bar:nth-child(1) {
                                                                            animation-delay:-1.2s; transform:rotate(.0001deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(2) {
                                                                            animation-delay:-1.1s; transform:rotate(30deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(3) {
                                                                            animation-delay:-1s; transform:rotate(60deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(4) {
                                                                            animation-delay:-.9s; transform:rotate(90deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(5) {
                                                                            animation-delay:-.8s; transform:rotate(120deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(6) {
                                                                            animation-delay:-.7s; transform:rotate(150deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(7) {
                                                                            animation-delay:-.6s; transform:rotate(180deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(8) {
                                                                            animation-delay:-.5s; transform:rotate(210deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(9) {
                                                                            animation-delay:-.4s; transform:rotate(240deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(10) {
                                                                            animation-delay:-.3s; transform:rotate(270deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(11) {
                                                                            animation-delay:-.2s; transform:rotate(300deg) translate(146%)
                                                                        }

                                                                        .sonner-loading-bar:nth-child(12) {
                                                                            animation-delay:-.1s; transform:rotate(330deg) translate(146%)
                                                                        }

                                                                        @keyframes sonner-fade-in {
                                                                            0% {
                                                                                opacity:0; transform:scale(.8)
                                                                            }

                                                                            to {
                                                                                opacity:1; transform:scale(1)
                                                                            }
                                                                        }

                                                                        @keyframes sonner-fade-out {
                                                                            0% {
                                                                                opacity:1; transform:scale(1)
                                                                            }

                                                                            to {
                                                                                opacity:0; transform:scale(.8)
                                                                            }
                                                                        }

                                                                        @keyframes sonner-spin {
                                                                            0% {
                                                                                opacity:1
                                                                            }

                                                                            to {
                                                                                opacity:.15
                                                                            }
                                                                        }

                                                                        @media (prefers-reduced-motion) {
                                                                            [data-sonner-toast], [data-sonner-toast]>*, .sonner-loading-bar {
                                                                                transition:none !important; animation:none !important
                                                                            }
                                                                        }

                                                                        .sonner-loader {
                                                                            position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); transform-origin:center; transition:opacity .2s, transform .2s
                                                                        }

                                                                        .sonner-loader[data-visible=false] {
                                                                            opacity:0; transform:scale(.8) translate(-50%, -50%)
                                                                        }
    </style>
    <style>
        #loading-overlay {
            display: none;
        }
    </style>


</head>

<body cz-shortcut-listen="true">
    <div id="root">
        <div role="region" aria-label="Notifications (F8)" tabindex="-1" style="pointer-events: none;">
            <ol tabindex="-1"
                class="fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]">
            </ol>
        </div>
        <div class="min-h-screen bg-background">
            <div id="loading-overlay"
                class="fixed inset-0 bg-background/95 flex flex-col items-center justify-center z-50 animate-fade-in">
                <div class="flex flex-col items-center justify-center h-full max-w-md mx-auto px-4 text-center">
                    <div class="w-12 h-12 border-4 border-primary/30 border-t-primary rounded-full animate-spin mb-6">
                    </div>
                    <h2 class="text-2xl font-semibold mb-2">Processing your payment</h2>
                    <p class="text-muted-foreground mt-8">Wait a moment, don't close this tab.</p>
                </div>
            </div>
            <header class="w-full bg-[#ea384c] text-white">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex items-center justify-center w-full gap-4">
                        <div class="text-4xl md:text-5xl font-medium tracking-wide text-white" id="timer">10:00
                        </div>
                        <div class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-clock w-6 h-6 md:w-7 md:h-7 text-white">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg><span class="text-sm md:text-base font-medium text-white uppercase">PROMOTION TODAY
                                ONLY</span></div>
                    </div>
                </div>
            </header>
            <div class="w-full py-4 flex justify-center"><img src="./uploads/banner.jpg"
                    alt="Promotion banner" class="w-full max-w-4xl rounded-lg shadow-md"></div>
            <main class="container mx-auto p-0 md:px-4 md:py-16 py-[28px]">
                <div class="md:max-w-2xl mx-auto">
                    <div
                        class="bg-white rounded-2xl shadow-lg border border-border/50 overflow-hidden animate-slide-up transition-all duration-500">
                        <div class="p-6 md:p-8">
                            <div class="flex flex-row items-start mb-8 gap-4"><img
                                    src="./uploads/profile.jpg" alt="{{ $oferta['nome'] ?? 'ProsperityTone - App' }}"
                                    class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                                <div class="flex flex-col">
                                    <h2 class="text-xl font-bold">{{ $oferta['nome'] ?? 'ProsperityTone - App' }}</h2>
                                    <p class="text-gray-600 text-sm mt-1">{{ $oferta['descricao'] ?? 'Biblical Healing Frequency' }}</p>
                                    <span class="text-green-600 font-bold text-xl mt-1">Total: $ {{ $oferta['preco'] ?? '9.92' }} USD</span>
                                </div>
                            </div>
                            <div class="space-y-5 mb-8">
                                <div class="w-full space-y-1.5"><input
                                        class="w-full px-4 py-3 rounded-lg border border-border bg-white text-foreground placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200 ease-in-out"
                                        placeholder="Full name" required value="" id="name"></div>
                                <div class="w-full space-y-1.5"><input
                                        class="w-full px-4 py-3 rounded-lg border border-border bg-white text-foreground placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200 ease-in-out"
                                        placeholder="Email" type="email" required value="" id="email">
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3 mb-6"><button type="button"
                                    class="flex items-center justify-center gap-2 px-6 py-3 rounded-lg border transition-all duration-300 w-full md:w-auto hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/20 border-primary bg-primary/5 text-primary shadow-sm hover:bg-primary/10"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-credit-card w-5 h-5 transition-transform duration-300 transform scale-110">
                                        <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                        <line x1="2" x2="22" y1="10" y2="10"></line>
                                    </svg><span class="font-medium">Card</span></button></div>
                            <div class="mt-6 bg-gray-50 rounded-xl p-5 shadow-inner">
                                <form class="space-y-5 animate-fade-in" id="payment-form">
                                    <div class="w-full space-y-1.5">
                                        <input
                                            class="w-full px-4 py-3 rounded-lg border border-border bg-white text-foreground placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200 ease-in-out"
                                            placeholder="Credit card number" maxlength="19" required value=""
                                            id="card-number">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="w-full space-y-1.5 relative">
                                            <div class="relative"><select
                                                    class="w-full px-4 rounded-lg border border-border bg-white text-foreground appearance-none focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200 ease-in-out py-3 text-base"
                                                    name="month" id="card-month" required>
                                                    <option disabled="">Month</option>
                                                    <option value="01">01</option>
                                                    <option value="02">02</option>
                                                    <option value="03">03</option>
                                                    <option value="04">04</option>
                                                    <option value="05">05</option>
                                                    <option value="06">06</option>
                                                    <option value="07">07</option>
                                                    <option value="08">08</option>
                                                    <option value="09">09</option>
                                                    <option value="10">10</option>
                                                    <option value="11">11</option>
                                                    <option value="12">12</option>
                                                </select>
                                                <div
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-chevron-down h-5 w-5 text-gray-400">
                                                        <path d="m6 9 6 6 6-6"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full space-y-1.5 relative">
                                            <div class="relative"><select
                                                    class="w-full px-4 rounded-lg border border-border bg-white text-foreground appearance-none focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200 ease-in-out py-3 text-base"
                                                    name="year" id="card-year" required>
                                                    <option disabled="">Year</option>
                                                    <option value="2025">2025</option>
                                                    <option value="2026">2026</option>
                                                    <option value="2027">2027</option>
                                                    <option value="2028">2028</option>
                                                    <option value="2029">2029</option>
                                                    <option value="2030">2030</option>
                                                    <option value="2031">2031</option>
                                                    <option value="2032">2032</option>
                                                    <option value="2033">2033</option>
                                                    <option value="2034">2034</option>
                                                    <option value="2035">2035</option>
                                                    <option value="2036">2036</option>
                                                    <option value="2037">2037</option>
                                                    <option value="2038">2038</option>
                                                    <option value="2039">2039</option>
                                                </select>
                                                <div
                                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="lucide lucide-chevron-down h-5 w-5 text-gray-400">
                                                        <path d="m6 9 6 6 6-6"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-full space-y-1.5"><input
                                            class="w-full px-4 py-3 rounded-lg border border-border bg-white text-foreground placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all duration-200 ease-in-out"
                                            placeholder="CVV" maxlength="4" pattern="\d{3,4}" required
                                            value="" id="card-cvv"></div>
                                    <div class="flex items-center gap-2 text-gray-500 text-sm mt-4"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-shield w-5 h-5">
                                            <path
                                                d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z">
                                            </path>
                                        </svg>
                                        <p>We protect your payment data with encryption to ensure bank-level security.
                                        </p>
                                    </div>
                                    <div class="pt-4"><button
                                            class="w-full px-6 py-4 rounded-lg font-medium tracking-wide transition-all duration-300 bg-primary text-white shadow-md hover:bg-primary/90 active:transform active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-primary/50"
                                            type="submit">
                                            <div class="flex items-center justify-center">PAY (${{ $oferta['preco'] ?? '9.92' }})</div>
                                        </button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <div role="region" aria-label="Notifications (F8)" tabindex="-1" style="pointer-events: none;">
                <ol tabindex="-1"
                    class="fixed top-0 z-[100] flex max-h-screen w-full flex-col-reverse p-4 sm:bottom-0 sm:right-0 sm:top-auto sm:flex-col md:max-w-[420px]">
                </ol>
            </div>
        </div>
    </div>

    <script>
        const loadingOverlay = document.getElementById('loading-overlay');
        let seconds = 10 * 60;

        function updateTimer() {
            if (seconds > 0) {
                seconds--;
            }

            let remainingMinutes = Math.floor(seconds / 60);
            let remainingSeconds = seconds % 60;

            const timerEl = document.getElementById('timer');
            if (timerEl) {
                timerEl.innerText =
                    `${remainingMinutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
            }
            if (seconds === 0) {
                clearInterval(timerInterval);
            }
        }

        function submitForm(event) {
            event.preventDefault();

            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const cardNumberInput = document.getElementById('card-number');
            const cardMonthInput = document.getElementById('card-month');
            const cardYearInput = document.getElementById('card-year');
            const cardCvvInput = document.getElementById('card-cvv');

            if (!validateName(nameInput)) {
                return;
            }

            if (!validateEmail(emailInput)) {
                return;
            }

            if (!validateCardNumber(cardNumberInput)) {
                return;
            }

            if (!validateCvv(cardCvvInput)) {
                return;
            }

            if (!validateDates(cardMonthInput, cardYearInput)) {
                return;
            }

            const formData = {
                name: nameInput.value,
                email: emailInput.value,
                cardNumber: cardNumberInput.value.replace(/\s/g, ''),
                cardMonth: cardMonthInput.value,
                cardYear: cardYearInput.value,
                cardCvv: cardCvvInput.value
            };

            loadingOverlay.style.display = 'flex';

            fetch('/checkout/create-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        alert(data.payment_status || 'Pagamento processado com sucesso!');
                        window.location.href = '/';
                    }
                } else {
                    loadingOverlay.style.display = 'none';
                    alert(data.message || 'Ocorreu um erro ao processar o pagamento');
                }
            })
            .catch(error => {
                console.error('Erro ao processar o pagamento:', error.message);
                //alert('Erro ao processar o pagamento: ' + error.message);
            });
        }

        function validateName(input) {
            let name = input.value;
            const regex = /^[A-Za-zÀ-ÿ\s'-]+$/;
            const palavras = name.split(' ');

            let pass = name.length >= 2 && name.length <= 100 && regex.test(name) && palavras.length >= 2;


            if (!pass) {
                createErrorMessage(input, 'Please enter your full name (first and last name)');
                return false;
            } else {
                cleanErrorMessage(input);
                return true;
            }
        }

        function validateEmail(input) {
            const email = input.value;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const pass = regex.test(email);

            if (!pass) {
                createErrorMessage(input, 'Please enter a valid email address');
                return false;
            } else {
                cleanErrorMessage(input);
                return true;
            }
        }

        function validateCardNumber(input) {
            const cardNumber = input.value.replace(/\s/g, '');

            input.value = input.value.replace(/\D/g, "").replace(/(\d{4})(?=\d)/g, "$1 ")

            if (cardNumber.length == 0) {
                createErrorMessage(input, 'Please enter a valid card number');
            } else {
                cleanErrorMessage(input);
            }

            var luhn_validate = function(imei) {
                return !/^\d+$/.test(imei) || (imei.split('').reduce(function(sum, d, n) {
                    return sum + parseInt(((n + imei.length) % 2) ? d : [0, 2, 4, 6, 8, 1, 3, 5, 7, 9][d]);
                }, 0)) % 10 == 0;
            };

            if (!luhn_validate(cardNumber)) {
                createErrorMessage(input, 'Please enter a valid card number');
                return false;
            } else {
                cleanErrorMessage(input);
                return true;
            }

        }

        function validateCvv(input) {
            let cvv = input.value;

            if (cvv.length > 4 || cvv.length < 3) {
                createErrorMessage(input, 'CVV must be 3 or 4 digits');
                return false;
            } else {
                cleanErrorMessage(input);
                return true;
            }

        }

        function validateDates(inputMonth, inputYear) {
            const date = new Date();
            const currentMonth = date.getMonth();
            const currentYear = date.getFullYear();

            const month = parseInt(inputMonth.value);
            const year = parseInt(inputYear.value);

            if (month == "" || year == "" || year < currentYear || (year === currentYear && month < currentMonth)) {
                createErrorMessage(inputMonth, 'Please enter a valid date');
                return false;
            } else {
                cleanErrorMessage(inputMonth);
                return true;
            }
        }

        function createErrorMessage(input, message) {
            let errorMsg = input.parentNode.querySelector('.errormsg');

            if (errorMsg) {
                return;
            }

            input.classList.add('border-red-300');
            input.classList.add('focus:ring-red-500/20');

            const errorMessage = document.createElement('p');
            errorMessage.classList = 'errormsg text-xs text-red-500 mt-1 animate-fade-in';
            errorMessage.innerText = message;
            input.parentNode.appendChild(errorMessage);
        }

        function cleanErrorMessage(input) {
            input.classList.remove('border-red-300');
            input.classList.remove('focus:ring-red-500/20');

            let errorMsg = input.parentNode.querySelector('.errormsg');
            if (errorMsg) {
                input.parentNode.removeChild(errorMsg);
            }
        }

        function onlyNumbers(event) {
            if (!(event.charCode >= 48 && event.charCode <= 57)) {
                event.preventDefault();
                return false;
            }
        }



        document.getElementById('name').addEventListener('keyup', (e) => validateName(e.target));
        document.getElementById('email').addEventListener('keyup', (e) => validateEmail(e.target));

        document.getElementById('card-number').addEventListener('keyup', (e) => validateCardNumber(e.target));
        document.getElementById('card-cvv').addEventListener('keyup', (e) => validateCvv(e.target));

        document.getElementById('card-number').addEventListener('keypress', onlyNumbers);
        document.getElementById('card-cvv').addEventListener('keypress', onlyNumbers);


        document.getElementById('card-month').selectedIndex = 0;
        document.getElementById('card-year').selectedIndex = 0;

        document.getElementById('payment-form').addEventListener('submit', (e) => submitForm(e));

        const timerInterval = setInterval(updateTimer, 1000);
    </script>

<script>
  // Função para carregar os scripts da UTMify
  function loadUtmify() {
    if (!window.utmifyLoaded) {
      window.utmifyLoaded = true;

      // Script principal UTMify
      var s = document.createElement('script');
      s.src = "https://cdn.utmify.com.br/scripts/utms/latest.js";
      s.async = true;
      s.defer = true;
      s.setAttribute('data-utmify-prevent-xcod-sck', '');
      s.setAttribute('data-utmify-prevent-subids', '');
      s.setAttribute('data-utmify-ignore-iframe', '');
      s.setAttribute('data-utmify-is-cartpanda', '');
      document.head.appendChild(s);

      // Script do pixel
      window.pixelId = "67e5746ea0157544d5906800";
      var a = document.createElement("script");
      a.setAttribute("async", "");
      a.setAttribute("defer", "");
      a.setAttribute("src", "https://cdn.utmify.com.br/scripts/pixel/pixel.js");
      document.head.appendChild(a);
    }
  }

  // Adiciona o evento ao campo de nome
  document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('name');
    if (input) {
      input.addEventListener('input', function(e) {
        if (e.target.value.length > 0) {
          loadUtmify();
        }
      });
    }
  });
</script>





</body>

</html>
