<script setup>
import { defineProps } from "vue";
const props = defineProps(["width", "height", "fill"]);
</script>
<template>
    <div>
        <svg
            :style="{
                width: props.width,
                height: props.height,
                fill: props.fill,
            }"
            style="transform: rotate(180deg)"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 32 32"
        >
            <circle transform="translate(8 0)" cx="0" cy="16" r="0">
                <animate
                    attributeName="r"
                    values="0; 4; 0; 0"
                    dur="1.2s"
                    repeatCount="indefinite"
                    begin="0"
                    keytimes="0;0.2;0.7;1"
                    keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8"
                    calcMode="spline"
                />
            </circle>
            <circle transform="translate(16 0)" cx="0" cy="16" r="0">
                <animate
                    attributeName="r"
                    values="0; 4; 0; 0"
                    dur="1.2s"
                    repeatCount="indefinite"
                    begin="0.3"
                    keytimes="0;0.2;0.7;1"
                    keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8"
                    calcMode="spline"
                />
            </circle>
            <circle transform="translate(24 0)" cx="0" cy="16" r="0">
                <animate
                    attributeName="r"
                    values="0; 4; 0; 0"
                    dur="1.2s"
                    repeatCount="indefinite"
                    begin="0.6"
                    keytimes="0;0.2;0.7;1"
                    keySplines="0.2 0.2 0.4 0.8;0.2 0.6 0.4 0.8;0.2 0.6 0.4 0.8"
                    calcMode="spline"
                />
            </circle>
        </svg>
    </div>
</template>

<style scoped></style>
