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
  },
};

function buidlStyles() {
  return src(paths.styles.entry)
    .pipe(
      sass({
        style: "compressed", // "compressed" or "expanded"
      }).on("error", sass.logError),
    )
    .pipe(dest(paths.styles.dest));
}

async function buildScripts() {
  return src(paths.scripts.entry)
    .pipe(
      esbuild({
        outfile: paths.scripts.bundleName,
        bundle: true,
        minify: true,
        platform: "browser",
      }),
    )
    .pipe(dest(paths.scripts.dest));
}

function watcher() {
  watch(paths.styles.watch, buidlStyles);
  watch(paths.scripts.watch, buildScripts);
}

export const build = parallel(buidlStyles, buildScripts);
export const dev = series(build, watcher);
