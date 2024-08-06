<script setup>
import { Link } from '@inertiajs/inertia-vue3';

</script>

<template>
Inertiaテスト<br>
<a href="/">aタグから来ました</a><br>
<Link href="/">Linkから来ました</Link>
<Link :href="route('inertia.index')">名前付きrouteの確認</Link>

</template>