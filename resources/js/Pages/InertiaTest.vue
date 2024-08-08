<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import { ref } from 'vue'

const newTitle = ref('')
const newContent = ref('')
</script>

<template>
Inertiaテスト<br>
<a href="/">aタグから来ました</a><br>
<Link href="/">Linkから来ました</Link><br>
<Link :href="route('inertia.index')">名前付きrouteの確認</Link><br>
<Link :href="route('inertia.show', { id:1 })">ルートパラメータの確認</Link>

<div class="mb-8"></div>
<input type="text" name="newTitle" v-model="newTitle">{{ newTitle }}<br>
<input type="text" name="newContent" v-model="newContent">{{ newContent }}<br>
<Link as="button" 
    :href="route('inertia.store')" 
    method="post"
    :data="{
        title: newTitle,
        content: newContent
    }">DB保存
</Link>

</template>