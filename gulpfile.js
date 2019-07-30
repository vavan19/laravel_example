const gulp = require('gulp');
const ftp = require('vinyl-ftp');
const gutil = require('gutil');
require('dotenv').config();

var localFiles = [
    './app/**/*',
    './bootstrap/**/*',
    './config/**/*',
    './database/**/*',
    './public/**/*',
    './resources/**/*',
    './routes/**/*',
    './vendor/**/*',
    'artisan',
    'composer.*',
    'package.json',
    'server.php',
];
function getFtpConnection(){
    return ftp.create({
        host: process.env.FTP_HOST,
        port: process.env.FTP_PORT,
        user: process.env.FTP_USER,
        password: process.env.FTP_PASS,
        log: gutil.log
    });
}
gulp.task('ftp', function(){
    var conn = getFtpConnection();
    return gulp.src(localFiles, {base: '.', buffer: false})
        .pipe(conn.newerOrDifferentSize('htdocs/tagatuba/'))
        .pipe(conn.dest('htdocs/tagatuba/'))
});
