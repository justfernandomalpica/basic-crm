import { dest, src, series, parallel, watch } from "gulp";
import * as dartSass from "sass";
import gulpSass from "gulp-sass";
import * as esbuild from "esbuild";

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
  statics: {
    vendor: "./src/js/vendor/**/*.js",
    dest: "./public/build/statics",
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

async function buildScripts(isProduction) {
  await esbuild.build({
    entryPoints: [paths.scripts.entry],
    bundle: true,
    minify: isProduction,
    outfile: `${paths.scripts.dest}/${paths.scripts.bundleName}`,
  });
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

function buildStatics() {
  return src(paths.statics.vendor).pipe(dest(paths.statics.dest));
}

export const common = buildStatics;

export const build = parallel(buildStylesProd, buildScriptsProd, common);
export const dev = series(
  parallel(buildStylesDev, buildScriptsDev, common),
  watcher,
);
