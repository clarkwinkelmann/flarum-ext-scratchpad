import app from 'flarum/forum/app';
import showPhpErrors from '../common/showPhpErrors';

app.initializers.add('clarkwinkelmann-scratchpad', () => {
    showPhpErrors();
});
