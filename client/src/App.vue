<template>
  <div id="q-app">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style>
.center-card {
  font-weight:300;
  max-width:900px;
  margin-left:auto;
  margin-right:auto
}
.card-title {
  /*Diff: not there*/
  padding-top: 8px;
  padding-bottom: 8px;
}
.card-title:first-child {
  margin-left: -24px;
  padding: 2px 10px 2px 24px;
  background: #e0e0e0;
  color: #616161;
  position: relative;
  border-radius: 3px 5px 5px 0;
                        /*Diff: not there*/
  display: inline;
  font-size: 1.25rem;
  font-weight: 500;
  line-height: 2rem;
  letter-spacing: 0.0125em;
}
.card-title:first-child:after {
  content: "";
  position: absolute;
  top: 100%;
  left: 0;
  width: 0;
  height: 0;
  border: 0 solid transparent;
  border-top-color: #bebebe;
  border-width: 9px 0 0 11px;
}
</style>
