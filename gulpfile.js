import { dest, src, series, watch } from "gulp";
import dartSass from "sass";
import gulpSass from "gulp-sass";

const sass = gulpSass(dartSass);

const paths = {
  styles: {
    entry: "./src/sass/app.scss",
    watch: "./src/sass/**/*.scss",
    dest: "./public/build/css",
  },
};

function buidlStyles() {
  return src(paths.styles.entry)
    .pipe(
      sass({
        style: "compressed",
      }).on("error", sass.logError),
    )
    .pipe(dest(paths.styles.dest));
}

function watcher() {
  watch(paths.styles.watch, buidlStyles);
}

export { buidlStyles };
export const dev = series(buidlStyles, watcher);
export const build = buidlStyles;
