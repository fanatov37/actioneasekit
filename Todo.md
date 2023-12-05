# 1. add Delayed additional exception (vat) use merge like check CmdConsumer +
# 2. add Delayed cmd exception (vat need check logic) -
# 3. add result for action service (success true)
# 4. add helperException (from position) +
# 5. parent::__construct("Send to delayed=>{$this->queueDelayed} | {$this->delay}"); | check this msg if new DelayedException will be +
# 6. Consymer use $this for static +-
# 7. check for remove logger from AbstractCmdConsumer