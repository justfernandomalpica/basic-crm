import { dest, src, series, parallel, watch } from "gulp";
import * as dartSass from "sass";
import gulpSass from "gulp-sass";
import esbuild from "gulp-esbuild";

const sass = gulpSass(dartSass);

const paths = {
  styles: {
    entry: "./src/sass/app.scss",
    watch: "./src/sass/**/*.scss",
    dest: "./public/build/css",
  },
  scripts: {
    entry: "./src/js/app.js",
    watch: "./src/js/**/*.js",
    dest: "./public/build/js",
    bundleName: "bundle.min.js",
    devName: "bundle.js",
  },
};

function buildStyles(isProduction) {
  return src(paths.styles.entry)
    .pipe(
      sass({
        style: isProduction ? "compressed" : "expanded", // "compressed" or "expanded"
      }).on("error", sass.logError),
    )
    .pipe(dest(paths.styles.dest));
}

function buildScripts(isProduction) {
  return src(paths.scripts.entry)
    .pipe(
      esbuild({
        outfile: isProduction
          ? paths.scripts.bundleName
          : paths.scripts.devName,
        bundle: true,
        minify: isProduction,
        platform: "browser",
      }),
    )
    .pipe(dest(paths.scripts.dest));
}

function buildStylesDev() {
  return buildStyles(false);
}
function buildStylesProd() {
  return buildStyles(true);
}
function buildScriptsDev() {
  return buildScripts(false);
}
function buildScriptsProd() {
  return buildScripts(true);
}

function watcher() {
  watch(paths.styles.watch, buildStylesDev);
  watch(paths.scripts.watch, buildScriptsDev);
}

export const build = parallel(buildStylesProd, buildScriptsProd);
export const dev = series(parallel(buildStylesDev, buildScriptsDev), watcher);
