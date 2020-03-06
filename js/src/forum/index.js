import app from 'flarum/app';
import showPhpErrors from '../common/showPhpErrors';

app.initializers.add('clarkwinkelmann-scratchpad', () => {
    showPhpErrors();
});
